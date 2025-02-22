<template>
    <ul class="py-2 px-4 text-sm">

        <li  class="p-2">
            <button @click="$emit('MenuBarClose')" class="absolute top-2 right-2  ml-2">
                <svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 -960 960 960" width="18px" fill="black">
                    <path
                    d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z" />
                </svg>
            </button>
        </li>
        <li @click="$emit('newGroupSection')" class="p-1 hover:bg-gray-100 cursor-pointer ">Group conversation</li>
        
        <li   @click="$emit('newPrivateSection')" class="p-1 hover:bg-gray-100 cursor-pointer">New conversation</li>
        <li class="p-1 hover:bg-gray-100 cursor-pointer">Settings</li>
    </ul>

   

    
</template>

<script>
export default {

    data(){
        return{
           
        }
    },

    methods:{
        
    }
}
</script>

<style>

</style>