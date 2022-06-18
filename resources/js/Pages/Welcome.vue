<!-- This example requires Tailwind CSS v2.0+ -->
<template>
    <div class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:text-center">
                <h1 class="font-bold text-6xl"><span class="text-red-400">Name</span><span class="text-gray-400">space</span></h1>
            </div>

            <div class="mt-10">
                <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    <div v-for="item in stats" :key="item.id" class="relative bg-white pt-5 px-4 pb-12 sm:pt-6 sm:px-6 shadow rounded-lg overflow-hidden">
                        <dt>
                            <div class="absolute bg-red-400 rounded-md p-3">
                                <component :is="icons[item.id]" class="h-6 w-6 text-white" aria-hidden="true" />
                            </div>
                            <p class="ml-16 text-sm font-medium text-gray-500 truncate">{{ item.name }}</p>
                        </dt>
                        <dd class="ml-16 pb-6 flex items-baseline sm:pb-7">
                            <p class="text-2xl font-semibold text-gray-900">
                                {{ item.stat }}
                            </p>
                            <div class="absolute bottom-0 inset-x-0 bg-gray-50 px-4 py-4 sm:px-6">
                                <div class="font-medium text-red-400 hover:text-red-700">
                                    <Link :href="panelRoutes[item.id]">View {{ item.name }}</Link>
                                </div>
                            </div>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3';
import { CursorClickIcon, MailOpenIcon, UsersIcon, MailIcon } from '@heroicons/vue/outline'

export default {
    name: "Welcome",
    components: {
        CursorClickIcon, MailOpenIcon, UsersIcon, MailIcon, Link
    },
    props: {
        stats: { type: Array }
    },
    data() {
        return {
            icons: [
                UsersIcon,
                MailOpenIcon,
                MailIcon
            ],
            panelRoutes: [
                this.route('bounty.active'),
                this.route('bounty.completed'),
                //todo make submissions view
                this.route('bounty.active')
            ]
        }
    },
}
</script>
