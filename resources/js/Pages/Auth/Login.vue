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
import BreezeCheckbox from '@/Components/Checkbox.vue';
import BreezeGuestLayout from '@/Layouts/Guest.vue';
import BreezeInput from '@/Components/Input.vue';
import BreezeLabel from '@/Components/Label.vue';
import BreezeValidationErrors from '@/Components/ValidationErrors.vue';
import {Head, Link, useForm} from '@inertiajs/inertia-vue3';
import { ethers } from 'ethers';
import axios from 'axios';

export default {
    name: 'Login',
    components: {
        BreezeGuestLayout,
        BreezeButton,
        Head,
        Link,
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
            if (!window.ethereum) {
                alert('MetaMask not detected. Please install MetaMask first.');
                return;
            }

            const provider = new ethers.providers.Web3Provider(window.ethereum);
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
