const {assert, expect} = require("chai");
const {ethers, upgrades} = require("hardhat");
const {BigNumber} = require("ethers");

describe("Namespace", function () {
    let namespace, owner, signer;

    before(async function (){
        const Namespace = await ethers.getContractFactory("Namespace");
        namespace = await upgrades.deployProxy(Namespace);
        await namespace.deployed();
        // [signer] = await ethers.provider.listAccounts()
        owner = await ethers.provider.getSigner(0)
        signer = await ethers.provider.getSigner(1);
    });

    it("should set the owner to be the deployer of the contract", async function () {
        // expect(await namespace.owner()).to.be.equal(signer);
        expect(await namespace.owner()).to.be.equal(await owner.getAddress());
    });

    describe("Bounty Creation", function () {
        it("will revert when the submission deadline isn't between 7 and 31 days", async function () {
            await expect(namespace.connect(signer).createBounty(6, {
                value: ethers.utils.parseEther('1')
            })).to.be.revertedWith("A Bounty cannot last for less than 7 or more than 31 days!");

            await expect(namespace.connect(signer).createBounty(32, {
                value: ethers.utils.parseEther('1')
            })).to.be.revertedWith("A Bounty cannot last for less than 7 or more than 31 days!");
        });

        it("will revert when the message value value isn't at least 1000000 gwei", async function () {
            await expect(namespace.connect(signer).createBounty(15, {
                value: ethers.utils.parseUnits('999999', 'gwei')
            })).to.be.revertedWith("The bounty must at least 1000000 gwei.")
        });

        //combine with "should emit BountyCreated"
        it("can create a bounty", async function () {
            //set timestamp of next block
            const timestamp = (await ethers.provider.getBlock(await ethers.provider.getBlockNumber())).timestamp + 50;
            await ethers.provider.send('evm_setNextBlockTimestamp', [timestamp])
            const twentyFiveDays = 25 * 24 * 60 * 60;

            const createBountyTx = await namespace.connect(signer).createBounty(25, {
                value: ethers.utils.parseEther('1')
            });

            await createBountyTx.wait();

            //assert bounty data
            const bounty = await namespace.getBounty(0);
            expect(bounty['total']).to.equal(ethers.utils.parseEther('.95'));
            expect(bounty['creator']).to.equal(await signer.getAddress());
            expect(bounty['winner']).to.equal(ethers.constants.AddressZero);
            expect(bounty['winningSubmission']).to.equal('');
            expect(bounty['submissionDeadline']).to.equal(BigNumber.from(timestamp + twentyFiveDays));

            //assert fee is transferred to owner escrow
            expect(await namespace.payments(await owner.getAddress())).to.be.equal(ethers.utils.parseEther('.05'));

            //assert bountiesCount increases
            expect(await namespace.bountiesCount()).to.be.equal(ethers.BigNumber.from('1'));
        });

        it("should emit the BountyCreated event with the correct arguments", async function () {
            //set timestamp of next block
            const timestamp = (await ethers.provider.getBlock(await ethers.provider.getBlockNumber())).timestamp + 50;
            await ethers.provider.send('evm_setNextBlockTimestamp', [timestamp])

            const thirtyDays = 30 * 24 * 60 * 60;
            await expect(namespace.connect(signer).createBounty(30, { value: ethers.utils.parseEther('1')}))
                .to.emit(namespace, 'BountyCreated')
                .withArgs(
                    1,
                    ethers.utils.parseEther('.95'),
                    await signer.getAddress(),
                    timestamp + thirtyDays
                );

            // simply see if an event was deployed
            // const createBountyTx = await namespace.createBounty(30, {
            //     value: ethers.utils.parseEther('1')
            // });
            // let receipt = await createBountyTx.wait();
            //
            // const topic = namespace.interface.getEventTopic('BountyCreated');
            // const log = receipt.logs.find(x => x.topics.indexOf(topic) >= 0);
            // const deployedEvent = namespace.interface.parseLog(log);
            //
            // assert(deployedEvent, "Expected the BountyCreated event to be emitted!");
        });
    });

    describe("Creator Declaring Bounty Winner", function () {
        let index;
        before(async function () {
            const createBountyTx = await namespace.connect(signer).createBounty(20, {
                value: ethers.utils.parseEther('1')
            });
            await createBountyTx.wait();
            index = (await namespace.bountiesCount()).toNumber() - 1
        });

        it("will revert when non-creator tries to declare winner", async function (){
            const winner = await  ethers.provider.getSigner(2);
            const winnerAddress = await winner.getAddress()

            //expect BountyWon to be emitted
            await expect(
                namespace.connect(winner).creatorDeclareWinner(index, winnerAddress , "I'm a winner!")
            ).to.be.revertedWith("Only the creator may declare a winner!");
        })

        it("will revert when creator tries to declare self as winner", async function (){
            const winnerAddress = await signer.getAddress()

            //expect BountyWon to be emitted
            await expect(
                namespace.connect(signer).creatorDeclareWinner(index, winnerAddress , "I'm a winner!")
            ).to.be.revertedWith("You can't win your own bounty.");
        })

        it("will revert when creator tries to end bounty before submission deadline", async function (){
            const winner = await  ethers.provider.getSigner(2);
            const winnerAddress = await winner.getAddress()

            //expect BountyWon to be emitted
            await expect(
                namespace.connect(signer).creatorDeclareWinner(index, winnerAddress , "I'm a winner!")
            ).to.be.revertedWith("Submission deadline hasn't been reached!");
        })

        it("bounty creator can declare a winner after the submission deadline", async function () {
            const twentyOneDays = 21 * 24 * 60 * 60;
            const timestamp = (await ethers.provider.getBlock(await ethers.provider.getBlockNumber())).timestamp + twentyOneDays;
            await ethers.provider.send('evm_setNextBlockTimestamp', [timestamp]);

            const winnerAddress = await ethers.provider.getSigner(2).getAddress()
            const total = (await namespace.getBounty(index)).total;

            //expect BountyWon to be emitted
            await expect(namespace.connect(signer).creatorDeclareWinner(index, winnerAddress , "I'm a winner!"))
                .to.emit(namespace, "BountyWon")
                .withArgs(
                    index,
                    total,
                    winnerAddress,
                    "I'm a winner!"
                );

            //confirm bounty variables
            const bounty = await namespace.getBounty(index);
            expect(bounty.winner).to.equal(winnerAddress);
            expect(bounty.winningSubmission).to.eq("I'm a winner!");
            expect(bounty.submissionDeadline).to.be.lt(timestamp);

            //confirm prize awarded to winner
            const prize = await namespace.payments(winnerAddress);
            expect(prize).to.be.equal(bounty.total);
        });

        it("will revert when a winner has already been declared", async function (){
            const winner = await  ethers.provider.getSigner(3);
            const winnerAddress = await winner.getAddress()

            //expect BountyWon to be emitted
            await expect(
                namespace.connect(signer).creatorDeclareWinner(index, winnerAddress , "I'm a winner!")
            ).to.be.revertedWith("Winner already declared.");
        })
    });

    describe("Owner Declaring Bounty Winner", function () {
        let index;
        before(async function () {
            const createBountyTx = await namespace.connect(signer).createBounty(20, {
                value: ethers.utils.parseEther('1')
            });
            await createBountyTx.wait();

            index = (await namespace.bountiesCount()).toNumber() - 1
        });

        it("will revert when owner tries to end bounty before submission deadline", async function (){
            const twentyOneDays = 21 * 24 * 60 * 60;
            const timestamp = (await ethers.provider.getBlock(await ethers.provider.getBlockNumber())).timestamp + twentyOneDays;
            await ethers.provider.send('evm_setNextBlockTimestamp', [timestamp]);

            const winner = await  ethers.provider.getSigner(2);
            const winnerAddress = await winner.getAddress()

            //expect BountyWon to be emitted
            await expect(
                namespace.connect(owner).ownerDeclareWinner(index, winnerAddress , "I'm a winner!")
            ).to.be.revertedWith("Creator has one week to pick a winning submission.");
        })

        it("owner can declare a winner seven days after the submission deadline", async function () {
            const twentyOnePlusSevenDays = ((21 + 7) * 24 * 60 * 60) + 1;
            const timestamp = (await ethers.provider.getBlock(await ethers.provider.getBlockNumber())).timestamp + twentyOnePlusSevenDays;
            await ethers.provider.send('evm_setNextBlockTimestamp', [timestamp]);

            const winnerAddress = await ethers.provider.getSigner(2).getAddress()
            const total = (await namespace.getBounty(index)).total;

            //expect BountyWon to be emitted
            await expect(namespace.connect(owner).ownerDeclareWinner(index, winnerAddress , "I'm a winner!"))
                .to.emit(namespace, "BountyWon")
                .withArgs(
                    index,
                    total,
                    winnerAddress,
                    "I'm a winner!"
                );

            //confirm bounty variables
            const bounty = await namespace.getBounty(index);
            expect(bounty.winner).to.equal(winnerAddress);
            expect(bounty.winningSubmission).to.eq("I'm a winner!");
            expect(bounty.submissionDeadline).to.be.lt(timestamp);
        });

        it("will revert when a winner has already been declared", async function (){
            const winner = await  ethers.provider.getSigner(3);
            const winnerAddress = await winner.getAddress()

            await expect(
                namespace.connect(owner).ownerDeclareWinner(index, winnerAddress , "I'm a winner!")
            ).to.be.revertedWith("Winner already declared.");
        })
    });

    describe("Withdrawing", function () {
        let winner, winnerAddress, ownerAddress;
        before(async function () {
            const createBountyTx = await namespace.connect(signer).createBounty(20, {
                value: ethers.utils.parseEther('1')
            });
            await createBountyTx.wait();

            const index = (await namespace.bountiesCount()).toNumber() - 1
            winner = await ethers.provider.getSigner(4);
            winnerAddress = await winner.getAddress();
            ownerAddress = await owner.getAddress();

            const twentyOneDays = (20 * 24 * 60 * 60) + 1;
            const timestamp = (await ethers.provider.getBlock(await ethers.provider.getBlockNumber())).timestamp + twentyOneDays;
            await ethers.provider.send('evm_setNextBlockTimestamp', [timestamp]);

            const declareWinnerTx = await namespace.connect(signer).creatorDeclareWinner(
                index,
                winnerAddress,
                "Winning Submission!"
            );
            await declareWinnerTx.wait();
        });

        it("winners can withdraw winnings", async function () {
            const prizeAmount = await namespace.payments(winnerAddress);
            expect(prizeAmount).to.be.gt(BigNumber.from('0'));

            const winnerBalanceBefore = await ethers.provider.getBalance(winnerAddress);
            await namespace.withdrawPayments(winnerAddress);
            const winnerBalanceAfter = await ethers.provider.getBalance(winnerAddress);
            expect(winnerBalanceAfter).to.be.gt(winnerBalanceBefore);

            const payableAmountRemaining = await namespace.payments(winnerAddress);
            expect(payableAmountRemaining).to.be.equal(BigNumber.from('0'));
        });

        it("owner can withdraw fees", async function () {
            const fees = await namespace.payments(ownerAddress);
            expect(fees).to.be.gt(BigNumber.from('0'));

            const ownerBalanceBefore = await ethers.provider.getBalance(ownerAddress);
            await namespace.withdrawPayments(ownerAddress);
            const ownerBalanceAfter = await ethers.provider.getBalance(ownerAddress);
            expect(ownerBalanceAfter).to.be.gt(ownerBalanceBefore);

            const payableAmountRemaining = await namespace.payments(ownerAddress);
            expect(payableAmountRemaining).to.be.equal(BigNumber.from('0'));
        });
    });
});


