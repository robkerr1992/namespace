<template>
    <Head title="Update User"/>
    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                New Bounty
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <BreezeValidationErrors class="mb-4"/>

                        <form @submit.prevent="submit">
                            <div>
                                <BreezeLabel for="value" value="Bounty Amount in ETH"/>
                                <BreezeInput id="value" type="text" class="mt-1 block w-full" v-model="value"
                                             required autofocus
                                             autocomplete="name"/>
                            </div>

                            <div>
                                <BreezeLabel for="deadline" value="Deadline in Days (Between 7 and 31)"/>
                                <BreezeInput id="deadline" type="text" class="mt-1 block w-full" v-model="deadline"
                                             required autofocus
                                             autocomplete="deadline"/>
                            </div>

                            <div class="mt-4">
                                <BreezeLabel for="description" value="Description"/>
                                <div>
                                    <div class="mt-1">
                                        <textarea
                                            rows="4"
                                            name="description"
                                            id="description"
                                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                            v-model="description"
                                        />
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <BreezeButton class="ml-4" :class="{ 'opacity-25': isLoading }"
                                              :disabled="isLoading">
                                    Create Bounty
                                </BreezeButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script>
import BreezeButton from '@/Components/Button.vue';
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated';
import BreezeInput from '@/Components/Input.vue';
import BreezeLabel from '@/Components/Label.vue';
import BreezeValidationErrors from '@/Components/ValidationErrors.vue';
import {Head, Link, useForm} from '@inertiajs/inertia-vue3';
import { ethers } from 'ethers';

import Abi from '@/artifacts/hardhat/contracts/Namespace.sol/Namespace.json';
const provider = new ethers.providers.Web3Provider(window.ethereum);
const contract = new ethers.Contract(
    process.env.MIX_CONTRACT_ADDRESS,
    Abi.abi,
    provider.getSigner()
);

export default {
    name: 'Create',
    components: {
        BreezeAuthenticatedLayout,
        BreezeButton,
        BreezeInput,
        BreezeLabel,
        BreezeValidationErrors,
        Head,
        Link,
    },
    data() {
        return {
            value: '.01',
            deadline: 30,
            description: 'Test Description',
            isLoading: false,
        }
    },
    methods: {
        async submit() {
            this.isLoading = true;
            try {
                //todo validate params
                //todo submit validated params
                //todo if successful submit params to backend
                const tx = await contract.createBounty(this.deadline, {value: ethers.utils.parseEther(this.value)});
                const txReceipt = await tx.wait();

                const topic = contract.interface.getEventTopic('BountyCreated');
                const log = txReceipt.logs.find(x => x.topics.indexOf(topic) >= 0);
                const logDescription = contract.interface.parseLog(log);

                console.log(logDescription.args);
                console.log(logDescription.args.id.toString());
                console.log(logDescription.args.total.toString());

                this.$inertia.post(this.route('bounty.store'), {
                    mapping_key: logDescription.args.id.toString(),
                    value: logDescription.args.total.toString(),
                    description: this.description,
                    deadline: logDescription.args.submissionDeadline.toString()
                });
            } catch (err) {
                console.log(err);
            }
            this.isLoading = false;

            // console.log(await this.provider.getBalance(await this.provider.getSigner().getAddress()));
            // const ethereum = await detectEthereumProvider();
            // const provider = new ethers.providers.Web3Provider(ethereum);
            // await provider.send("eth_requestAccounts", []);
            //
            // console.log(await provider.getSigner().getAddress());
            // console.log((await provider.getBalance(await provider.getSigner().getAddress())).toString());

            // const form = useForm({
            //     value: this.value,
            //     description: this.description
            // });
            //
            //
            // form.post(route('user.update'));
        }
    }
}

</script>
