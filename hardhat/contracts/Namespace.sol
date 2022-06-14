// SPDX-License-Identifier: UNLICENSED
pragma solidity ^0.8.4;

import "@openzeppelin/contracts-upgradeable/access/OwnableUpgradeable.sol";
import "@openzeppelin/contracts-upgradeable/security/PullPaymentUpgradeable.sol";
import "hardhat/console.sol";

contract Namespace is PullPaymentUpgradeable, OwnableUpgradeable {
    mapping(uint256 => Bounty) public bounties;
    uint256 public bountiesCount;

    struct Bounty {
        uint256 total;
        address creator;
        address winner;
        string winningSubmission;
        uint256 submissionDeadline;
    }

    event BountyCreated(
        uint256 id,
        uint256 total,
        address creator,
        uint256 submissionDeadline
    );

    event BountyWon(
        uint256 id,
        uint256 total,
        address winner,
        string winningSubmission
    );

    function initialize() public initializer {
        __Ownable_init();
        __PullPayment_init();
    }

    function getBounty(uint256 bountyId) external view returns (Bounty memory bounty) {
        return bounties[bountyId];
    }

    function createBounty(uint256 _submissionDeadlineInDays) external payable {
        require(
            _submissionDeadlineInDays > 7 && _submissionDeadlineInDays <= 31,
            "A Bounty cannot last for less than 7 or more than 31 days!"
        );

        require(msg.value > 1000000 gwei, "The bounty must at least 1000000 gwei.");

        //5% fee, the dev team thanks you!
        uint256 bountyTotal = msg.value * 95 / 100;
        _asyncTransfer(owner(), msg.value - bountyTotal);

        bounties[bountiesCount] = Bounty(
            bountyTotal,
            msg.sender,
            address(0),
            "",
            block.timestamp + (_submissionDeadlineInDays * 1 days)
        );

        Bounty storage bounty = bounties[bountiesCount];

        emit BountyCreated(
            bountiesCount,
            bounty.total,
            bounty.creator,
            bounty.submissionDeadline
        );

        bountiesCount++;
    }

    function creatorDeclareWinner(
        uint256 bountyId,
        address _winner,
        string memory _winningSubmission
    ) external {
        Bounty storage bounty = bounties[bountyId];

        require(msg.sender == bounty.creator, "Only the creator may declare a winner!");
        require(_winner != bounty.creator, "You can't win your own bounty.");
        require(block.timestamp > bounty.submissionDeadline, "Submission deadline hasn't been reached!");
        require(bounty.winner == address(0), "Winner already declared.");

        declareWinner(bountyId, bounty, _winner, _winningSubmission);
    }

    //We need a way to declare a winner in case the creator of the bounty doesn't
    //They have 7 days to pick a winning submission
    //Owner can be declared as winner if no submissions were made
    function ownerDeclareWinner(
        uint256 bountyId,
        address _winner,
        string memory _winningSubmission
    ) external onlyOwner {
        Bounty storage bounty = bounties[bountyId];

        require(bounty.winner == address(0), "Winner already declared.");
        require(block.timestamp > bounty.submissionDeadline + 7 days, "Creator has one week to pick a winning submission.");

        declareWinner(bountyId, bounty, _winner, _winningSubmission);
    }

    function declareWinner(
        uint256 bountyId,
        Bounty storage bounty,
        address _winner,
        string memory _winningSubmission
    ) internal {
        bounty.winner = _winner;
        bounty.winningSubmission = _winningSubmission;
        _asyncTransfer(_winner, bounty.total);

        emit BountyWon(bountyId, bounty.total, bounty.winner, bounty.winningSubmission);
    }
}
