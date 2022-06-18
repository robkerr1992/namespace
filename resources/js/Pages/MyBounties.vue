<template>
    <Head title="My Bounties"/>

    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                My Bounties
            </h2>
        </template>

        <div v-if="active" class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-2 ml-2">
                    Active
                </h2>
                <BountyGrid
                    :bounties="active"
                    :active="true"
                ></BountyGrid>
            </div>
        </div>

        <div v-if="completed" class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-2 ml-2">
                    Completed
                </h2>
                <BountyGrid
                    :bounties="completed"
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
    name: "MyBounties",
    components: {
        BreezeAuthenticatedLayout,
        BountyGrid,
        Head
    },
    props: {
        active: {
            type: Object, default: () => {}
        },
        completed: {
            type: Object, default: () => {}
        }
    }
}
</script>

<style scoped>

</style>
