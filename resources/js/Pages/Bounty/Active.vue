<template>
    <Head title="Active Bounties"/>

    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Active Bounties
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <BountyGrid
                    :bounties="bounties"
                    :active="true"
                ></BountyGrid>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import BountyGrid from "@/Components/BountyGrid";
import {Head} from '@inertiajs/inertia-vue3';

export default {
    name: "Active",
    components: {
        BreezeAuthenticatedLayout,
        BountyGrid,
        Head
    },
    props: {
        bounties: {
            type: Object, default: () => {}
        }
    }
}
</script>

<style scoped>

</style>
