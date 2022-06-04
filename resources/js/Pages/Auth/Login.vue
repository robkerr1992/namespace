<template>
    <BreezeGuestLayout>
        <Head title="Log in"/>

        <BreezeValidationErrors class="mb-4"/>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="web3Login">
            <div class="flex items-center justify-center mt-4">
                <BreezeButton class="ml-4" :class="{ 'opacity-25': loading }" :disabled="loading">
                    Log in with MetaMask
                </BreezeButton>
            </div>
        </form>
    </BreezeGuestLayout>
</template>

<script>
import BreezeButton from '@/Components/Button.vue';
import BreezeGuestLayout from '@/Layouts/Guest.vue';
import BreezeValidationErrors from '@/Components/ValidationErrors.vue';
import {Head, useForm} from '@inertiajs/inertia-vue3';
import { ethers } from 'ethers';
import axios from 'axios';
import detectEthereumProvider from '@metamask/detect-provider';

export default {
    name: 'Login',
    components: {
        BreezeGuestLayout,
        BreezeButton,
        Head,
        BreezeValidationErrors
    },
    props: {
        status: { type: String }
    },
    data() {
        return {
            loading: false
        }
    },
    methods: {
        async web3Login() {
            const ethereum = await detectEthereumProvider();

            if (!ethereum) {
                alert('MetaMask not detected. Please install MetaMask first.');
                return;
            }

            if (ethereum !== window.ethereum) {
                alert('You may have multiple wallets enabled. Please choose one!');
                return;
            }

            const provider = new ethers.providers.Web3Provider(ethereum);
            await provider.send("eth_requestAccounts", []);

            this.loading = true;

            try {
                const message = await axios.get('/web3-login-message');
                const address = await provider.getSigner().getAddress();
                const signature = await provider.getSigner().signMessage(message.data);

                const form = useForm({signature, address});

                form.post('/login-web3', {
                    onFinish: function () {
                        this.loading = false;
                    }
                });
            } catch (e) {
                console.log(e);
                this.loading = false;
            }
        }
    }
}

</script>
