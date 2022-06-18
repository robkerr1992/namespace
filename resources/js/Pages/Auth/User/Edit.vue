<template>
    <Head title="Update User"/>
    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Profile
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <BreezeValidationErrors class="mb-4"/>

                        <form @submit.prevent="submit">
                            <div>
                                <BreezeLabel for="name" value="Name"/>
                                <BreezeInput id="name" type="text" class="mt-1 block w-full" v-model="form.name"
                                             required autofocus
                                             autocomplete="name"/>
                            </div>

                            <div class="mt-4">
                                <BreezeLabel for="email" value="Email"/>
                                <BreezeInput id="email" type="email" class="mt-1 block w-full" v-model="form.email"
                                             required
                                             autocomplete="username"/>
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <BreezeButton class="ml-4" :class="{ 'opacity-25': form.processing }"
                                              :disabled="form.processing">
                                    Update
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

export default {
    name: 'UserEdit',
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
            form: useForm({
                name: this.$page.props.auth.user.name,
                email: this.$page.props.auth.user.email,
            })
        }
    },
    methods: {
        submit() {
            this.form.post(route('user.update'));
        }
    }
}

</script>
