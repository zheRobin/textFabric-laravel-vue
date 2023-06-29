<script setup>
    import {useForm} from "@inertiajs/vue3";

    const form = useForm({
        name: null,
        permission: {
            create: false,
            read: false,
            update: false,
            delete: false
        }
    })

    const create = () => {
        form.post(route('api.create'));
    }
</script>

<template>
    <div class="mx-auto px-6 py-6">
        <form @submit.prevent="create">
        <div class="grid grid-cols-3">
            <div class="col-end-2">
                <div class="font-normal text-3xl">Create API token</div>
                <p class="mt-5 text-xs w-3/4">API tokens allow third-party services to authenticate with our application on your behalf</p>
            </div>
            <div class="col-start-2 col-end-4">

                <div class="bg-white shadow sm:rounded-t-lg pt-5 pl-5">
                    <label for="name" class="block text-sm font-normal leading-6 text-gray-900">Name</label>
                    <div class="mt-2">
                        <input v-model="form.name" type="text" name="name" id="name" class="block w-3/4 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"  required/>
                    </div>
                    <div class="block text-sm font-normal leading-6 text-gray-900 mt-5">Permission</div>
                    <div class="grid grid-rows-2 grid-cols-2 gap-3 mt-3 pb-5">
                        <div class="relative flex items-start">
                            <div class="flex h-6 items-center">
                                <input v-model="form.permission.create" id="create" aria-describedby="create-description" name="create" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"/>
                            </div>
                            <div class="ml-3 text-sm leading-6">
                                <label for="create" class="font-normal text-gray-900">Create</label>
                            </div>
                        </div>
                        <div class="relative flex items-start">
                            <div class="flex h-6 items-center">
                                <input v-model="form.permission.read" id="read" aria-describedby="read-description" name="read" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" />
                            </div>
                            <div class="ml-3 text-sm leading-6">
                                <label for="read" class="font-normal text-gray-900">Read</label>
                            </div>
                        </div>
                        <div class="relative flex items-start">
                            <div class="flex h-6 items-center">
                                <input v-model="form.permission.update" id="update" aria-describedby="update-description" name="update" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" />
                            </div>
                            <div class="ml-3 text-sm leading-6">
                                <label for="update" class="font-normal text-gray-900">Update</label>
                            </div>
                        </div>
                        <div class="relative flex items-start">
                            <div class="flex h-6 items-center">
                                <input v-model="form.permission.delete" id="delete" aria-describedby="delete-description" name="delete" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" />
                            </div>
                            <div class="ml-3 text-sm leading-6">
                                <label for="delete" class="font-normal text-gray-900">Delete</label>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="flex justify-end pr-5 pb-3 pt-3 rounded-b-lg bg-gray-50">
                    <button type="submit" class="rounded-md bg-black px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500 tracking-widest">CREATE</button>
                </div>
            </div>
        </div>
        </form>
    </div>
</template>
<script setup>
</script>
