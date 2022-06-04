<template>
    <Head title="Test" />

    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        You're logged in!
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import {ethers} from "ethers";
import Abi from '@/artifacts/hardhat/contracts/Test.sol/Test.json';


export default {
    name: "Test",
    components: {
        BreezeAuthenticatedLayout
    },
    async created() {
        const contract = new ethers.Contract(
            process.env.MIX_CONTRACT_ADDRESS,
            Abi.abi,
            new ethers.providers.Web3Provider(window.ethereum).getSigner()
        );

        console.log((await contract.total()).toString());
    }
}
</script>

<style scoped>

</style>
