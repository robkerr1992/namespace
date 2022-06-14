require("@nomiclabs/hardhat-waffle");
require('@openzeppelin/hardhat-upgrades');
const mix = require('laravel-mix');

// This is a sample Hardhat task. To learn how to create your own go to
// https://hardhat.org/guides/create-task.html
task("accounts", "Prints the list of accounts", async (taskArgs, hre) => {
  const accounts = await hre.ethers.getSigners();

  for (const account of accounts) {
    console.log(account.address);
  }
});

// You need to export an object to set up your config
// Go to https://hardhat.org/config/ to learn more

/**
 * @type import('hardhat/config').HardhatUserConfig
 */

module.exports = {
  solidity: "0.8.4",
    // defaultNetwork: "rinkeby",
    networks: {
        hardhat: {
        },
        rinkeby: {
            url: process.env.MIX_PROVIDER_URL,
            accounts: [process.env.MIX_PRIVATE_KEY]
        }
    },
    paths: {
        sources: "./hardhat/contracts",
        tests: "./tests/contracts",
        cache: "./hardhat/cache",
        artifacts: "./resources/js/artifacts"
    },
};
