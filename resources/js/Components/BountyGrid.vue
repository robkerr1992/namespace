<template>
    <ul role="list" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <li v-for="bounty in bounties" :key="bounties.mapping_key"
            class="col-span-1 bg-white rounded-lg shadow divide-y divide-gray-200">
            <div class="w-full flex items-center justify-between p-6 space-x-6">
                <div class="flex-1 truncate">
                    <div class="flex items-center space-x-3">
                        <h3 class="text-gray-900 text-sm font-medium truncate">{{ formatEther(bounty.value) }}</h3><h3 class="text-gray-900 text-sm font-medium"> ETH</h3>
                        <span
                            class="flex-shrink-0 inline-block px-2 py-0.5 text-blue-800 text-xs font-medium bg-blue-100 rounded-full">
                            {{ bounty.submissions_count }} Submissions
                        </span>
                        <span
                            v-if="active && daysLeft(bounty) > 0"
                            class="flex-shrink-0 inline-block px-2 py-0.5 text-red-800 text-xs font-medium bg-red-100 rounded-full">
                            {{ daysLeft(bounty) }} Days Left
                        </span>
                    </div>
                    <p class="mt-1 text-gray-500 text-sm truncate">{{ bounty.description }}</p>
                </div>
                <!--                <img class="w-10 h-10 bg-gray-300 rounded-full flex-shrink-0" :src="person.imageUrl" alt=""/>-->
            </div>
            <div>
                <div class="-mt-px flex divide-x divide-gray-200">
                    <div class="w-0 flex-1 flex">
                        <Link :href="route('bounty.show', bounty.id)"
                              class="relative -mr-px w-0 flex-1 inline-flex items-center justify-center py-4 text-sm text-gray-700 font-medium border border-transparent rounded-bl-lg hover:text-gray-500">
                            <MailIcon class="w-5 h-5 text-gray-400" aria-hidden="true"/>
                            <span class="ml-3">View</span>
                        </Link>
                    </div>
                    <!--                    <div class="-ml-px w-0 flex-1 flex">-->
                    <!--                        <a :href="`tel:${person.telephone}`"-->
                    <!--                           class="relative w-0 flex-1 inline-flex items-center justify-center py-4 text-sm text-gray-700 font-medium border border-transparent rounded-br-lg hover:text-gray-500">-->
                    <!--                            <PhoneIcon class="w-5 h-5 text-gray-400" aria-hidden="true"/>-->
                    <!--                            <span class="ml-3">Call</span>-->
                    <!--                        </a>-->
                    <!--                    </div>-->
                </div>
            </div>
        </li>
    </ul>
</template>
<script>
import {MailIcon, PhoneIcon} from '@heroicons/vue/solid'
import {Link} from '@inertiajs/inertia-vue3';
import moment from "moment";
import {ethers} from 'ethers';

export default {
    name: "BountyGrid",
    components: {
        MailIcon,
        PhoneIcon,
        Link
    },
    props: {
        bounties: { type: Object, default: () => {} },
        active: { type: Boolean, default: () => false }
    },
    methods: {
        daysLeft(bounty) {
            return moment.unix(bounty.deadline).diff(moment.now(), 'days', false);
        },
        formatEther(wei) {
            return ethers.utils.formatEther(wei).toString()
        }
    }
}
</script>

<style scoped>

</style>
