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
                        <h3 class="text-lg leading-6 font-medium text-gray-900">{{ formatEther(bounty.value) }} ETH</h3>
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
                            <div v-if="acceptingSubmissions" class="sm:col-span-2">
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
                                                <span class="ml-2 flex-1 w-0"> {{ submission.submission }} </span>
                                            </div>
                                            <div class="ml-4 flex-shrink-0">
                                                <button type="button" v-if="canDeclareWinner" @click="declareWinner(submission.id)" class="font-medium text-indigo-600 hover:text-indigo-500"> {{ submission.submitter.eth_address }} </button>
                                                <a v-else class="font-medium text-indigo-600 hover:text-indigo-500"> {{ submission.submitter.eth_address }} </a>
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

        <TransitionRoot as="template" :show="open">
            <Dialog as="div" class="relative z-10" @close="open = false">
                <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
                </TransitionChild>

                <div class="fixed z-10 inset-0 overflow-y-auto">
                    <div class="flex items-end sm:items-center justify-center min-h-full p-4 text-center sm:p-0">
                        <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200" leave-from="opacity-100 translate-y-0 sm:scale-100" leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                            <DialogPanel class="relative bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full sm:p-6">
                                <div class="sm:flex sm:items-start">
                                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                        <ExclamationIcon class="h-6 w-6 text-red-600" aria-hidden="true" />
                                    </div>
                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                        <DialogTitle as="h3" class="text-lg leading-6 font-medium text-gray-900"> Declare Winner </DialogTitle>
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-500">Are you sure you want to declare <span class="text-red-400">{{ submissions.find(x => x.id === pickedSubmission).submission ?? '' }}</span> the winner? This action cannot be undone.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                    <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm" @click="submitWinner()" :disabled="awaitingTxResponse">Declare Winner</button>
                                    <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm" @click="open = false" ref="cancelButtonRef" :disabled="awaitingTxResponse">Cancel</button>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
    </BreezeAuthenticatedLayout>
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
        Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot
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
            open: false,
            pickedSubmission: null,
            awaitingTxResponse: false
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
            this.open = true;
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
            this.open = false;
            this.awaitingTxResponse = false;
        }
    }
}
</script>
