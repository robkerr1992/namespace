<template>
    <Head title="Bounty Details"/>

    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Bounty Details
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6">
                        <div class="flex justify-between">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">{{ formatEther(bounty.value) }} ETH</h3>
                            <BreezeButton
                                v-if="!bounty.claimed && winningSubmission && winningSubmission.submitter.id === $page.props.auth.user.id"
                                class="ml-4"
                                @click="claimOpened = true"
                                :class="{ 'opacity-25': awaitingTxResponse }"
                                :disabled="awaitingTxResponse"
                            >Claim Bounty</BreezeButton>
                        </div>
                        <p v-if="acceptingSubmissions" class="mt-1 max-w-2xl text-sm text-gray-500">Submissions accepted for next <span class="text-red-400">{{ deadline() }}</span></p>
                    </div>
                    <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Posted by {{ bounty.poster.eth_address }}</dt>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Description</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ bounty.description }}</dd>
                            </div>
                            <div v-if="acceptingSubmissions && bounty.user_id !== $page.props.auth.user.id" class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Add Submission</dt>
                                <BreezeValidationErrors class="mb-4"/>

                                <form @submit.prevent="submit">
                                    <div class="flex justify-between">
                                        <BreezeInput id="submission" type="text" class="mt-1 w-3/4"
                                                     required autofocus v-model="form.submission"
                                                     autocomplete="submission"/>
                                        <BreezeButton class="ml-4" :class="{ 'opacity-25': form.processing }"
                                                      :disabled="form.processing">
                                            Submit
                                        </BreezeButton>
                                    </div>
                                </form>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Submissions</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <ul v-if="submissions" role="list" class="border border-gray-200 rounded-md divide-y divide-gray-200">
                                        <li v-for="submission in submissions" class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                            <div class="w-0 flex-1 flex items-center">
                                                <StarIcon v-if="submission.won_at" class="flex-shrink-0 h-5 w-5 text-yellow-500" aria-hidden="true" />
                                                <DocumentTextIcon v-else class="flex-shrink-0 h-5 w-5 text-gray-400" aria-hidden="true" />
                                                <button type="button" v-if="canDeclareWinner" @click="declareWinner(submission.id)">
                                                    <span class="ml-2 flex-1 w-0 hover:text-red-500"> {{ submission.submission }} </span>
                                                </button>
                                                <span v-else class="ml-2 flex-1 w-0"> {{ submission.submission }} </span>
                                            </div>
                                            <div class="ml-4 flex-shrink-0">
                                                <a class="font-medium text-indigo-600 invisible md:visible"> {{ submission.submitter.eth_address }} </a>
                                            </div>
                                        </li>
                                    </ul>
                                    <span v-else>No Submissions Yet</span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>

    <Modal
        color="red"
        confirm-label="Confirm Winner"
        :open="confirmOpened"
        @close="confirmOpened = false"
        @confirmed="submitWinner"
    >
        <template #title>
            Confirm Winner
        </template>
        <template #message>
            Are you sure you want to declare <span class="text-red-400">{{ submissions.find(x => x.id === pickedSubmission).submission ?? '' }}</span> the winner? This action cannot be undone.
        </template>
    </Modal>

    <Modal
        color="red"
        confirm-label="Claim Bounty"
        :open="claimOpened"
        @close="claimOpened = false"
        @confirmed="claimBounty"
    >
        <template #title>
            Claim Bounty
        </template>
        <template #message>
            Claim <span class="text-red-400">{{ formatEther(this.bounty.value) }} ETH</span> ?
        </template>
    </Modal>
</template>

<script>
import BreezeButton from '@/Components/Button.vue';
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated';
import BreezeInput from '@/Components/Input.vue';
import BreezeLabel from '@/Components/Label.vue';
import BreezeValidationErrors from '@/Components/ValidationErrors.vue';
import BountyGrid from "@/Components/BountyGrid";
import {Head} from '@inertiajs/inertia-vue3';
import { DocumentTextIcon, StarIcon } from '@heroicons/vue/solid'
import moment from "moment";
import {ethers} from 'ethers';
import { useForm } from '@inertiajs/inertia-vue3';
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { ExclamationIcon } from '@heroicons/vue/outline'
import Modal from "@/Components/Modal";

import Abi from "@/artifacts/hardhat/contracts/Namespace.sol/Namespace.json";
const provider = new ethers.providers.Web3Provider(window.ethereum);
const contract = new ethers.Contract(
    process.env.MIX_CONTRACT_ADDRESS,
    Abi.abi,
    provider.getSigner()
);

export default {
    name: "Show",
    components: {
        Head,
        BreezeAuthenticatedLayout, BreezeButton, BreezeInput, BreezeLabel, BreezeValidationErrors,
        BountyGrid,
        DocumentTextIcon, StarIcon, ExclamationIcon,
        Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot,
        Modal
    },
    props: {
        bounty: {type: Object, default: () => {}},
        submissions: {type: Object, default: () => {}},
        winningSubmission: {type: Object, default: () => {}},
    },
    data() {
        return {
            form: useForm({
                submission: ''
            }),
            confirmOpened: false,
            claimOpened: false,
            pickedSubmission: null,
            awaitingTxResponse: false,
        }
    },
    computed: {
        acceptingSubmissions() {
            return !this.winningSubmission && moment.unix(this.bounty.deadline) > moment.now()
        },
        canDeclareWinner() {
            return this.bounty.user_id === this.$page.props.auth.user.id
                && !this.winningSubmission
                && moment.unix(this.bounty.deadline) < moment.now()
        }
    },
    methods: {
        deadline() {
            let time = moment.unix(this.bounty.deadline).diff(moment.now(), 'hours', true);
            return Math.floor(time/24).toString() + ' days ' + (time % 24 > 1 ? Math.floor(time % 24).toString() + ' hrs' : '~1 hr');
        },
        formatEther(wei) {
            return ethers.utils.formatEther(wei).toString()
        },
        submit() {
            this.form.post(this.route('submission.store', this.bounty.id), {
                onSuccess: () => {
                    this.form.reset();
                }
            })
        },
        declareWinner(submission) {
            this.pickedSubmission = submission;
            this.confirmOpened = true;
        },
        async submitWinner() {
            this.awaitingTxResponse = true;
            try {
                //todo validate submission

                const submission = this.submissions.find(x => x.id === this.pickedSubmission);
                const tx = await contract.creatorDeclareWinner(this.bounty.mapping_key, submission.submitter.eth_address, submission.submission);
                const txReceipt = await tx.wait();

                this.$inertia.post(this.route('declare-winner', [this.bounty.id, this.pickedSubmission]));
            } catch (err) {
                console.log(err);
            }
            this.confirmOpened = false;
            this.awaitingTxResponse = false;
        },
        async claimBounty() {
            console.log('claiming bounty');
            this.awaitingTxResponse = true;
            try {
                const address = await provider.getSigner().getAddress();
                const tx = await contract.withdrawPayments(address);
                const txReceipt = await tx.wait();

                this.$inertia.post(this.route('claim-bounty', [this.bounty.id]));
            } catch (err) {
                console.log(err);
            }

            this.awaitingTxResponse = false;
            this.claimOpened = false;
        }
    }
}
</script>
