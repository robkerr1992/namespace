const { ethers } = require("hardhat");

async function main () {
    const Test = await ethers.getContractFactory('Test');
    const test = await Test.deploy(222);
    await test.deployed();

    console.log(await test.total());
    console.log(test.address);
}

main()
    .then(() => process.exit(0))
    .catch((error) => {
        console.log(error);
        process.exit(1);
    });
