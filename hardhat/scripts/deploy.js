const {ethers, upgrades} = require("hardhat");

async function main () {
    const Namespace = await ethers.getContractFactory('Namespace');
    const namespace = await upgrades.deployProxy(Namespace);
    await namespace.deployed();

    console.log(await namespace.owner());
    console.log(namespace.address);
}

main()
    .then(() => process.exit(0))
    .catch((error) => {
        console.log(error);
        process.exit(1);
    });
