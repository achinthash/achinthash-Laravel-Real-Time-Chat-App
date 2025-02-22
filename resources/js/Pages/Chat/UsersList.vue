<template>

    <!-- New Group Section -->
    <div v-if="newGroupSection" class="absolute w-full h-full flex items-center justify-center z-[1] inset-0 bg-black bg-opacity-60 ">
        
        <NewGroup v-if="newGroupSection" @CloseNewGroup="newGroupSection = false"/>
        
    </div>

    <!-- New conversation Section -->
    <div v-if="newPrivateSection" class="absolute w-full h-full flex items-center justify-center z-[1] inset-0 bg-black bg-opacity-60 ">
        
        <NewConversation v-if="newPrivateSection" @CloseNewConversation="newPrivateSection = false"/>
        
    </div>

    


    <div class="relative"> 
        <div class="flex justify-between p-4 bg-gray-100"> 
            <h1 class="text-sm font-semibold  text-black"> Latest Chats</h1>
            <svg @click="menuBar" class="w-5 h-5 rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round"  stroke-linejoin="round"  stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
              </svg>
        </div>

         <!-- Dropdown Menu -->
         <div v-if="isMenuBar" class="absolute p-2 right-4 top-14 mt-2 bg-white shadow-lg rounded-lg border border-gray-200 z-10">
            <MenuView @MenuBarClose="isMenuBar = false" @newGroupSection="openNewGroupSection" @newPrivateSection="openPrivateSection" />
        </div>
       
    
        
        <div class="max-h-[80vh] overflow-y-auto "> 

            <!-- chat list -->

            <!-- private conversation list -->
            
            <PrivateConversationList :onlineUsers="onlineUsers" />


            <div class="p-4 bg-gray-100 mb-[2px]">
                <div class="flex items-center space-x-4">
                    <h2 class="text-base font-bold">Group conversation</h2>
                </div>
            </div>


            <!-- group conversations -->
            
            <GroupConversationsList />

       
        </div>

        
    </div>
 
</template>

<script>

import MenuView from './MenuView.vue';

import NewGroup from './NewGroup.vue';
import GroupConversationsList from './GroupConversationsList.vue';
import NewConversation from './NewConversation.vue';
import PrivateConversationList from './PrivateConversationList.vue';


import { defineProps } from 'vue';



export default {
    
    name: "UserList",
    data(){
        return{
            isMenuBar : false,
            newGroupSection: false,
            newPrivateSection : false
        }
    },
    props:{
        onlineUsers:{
            type: Array,
            required : true
        }
    }, 

    components:{
        MenuView,
        NewGroup,
        GroupConversationsList,
        NewConversation,
        PrivateConversationList

    },
    methods:{

        menuBar(){
            this.isMenuBar = !this.isMenuBar;
        },
        openNewGroupSection() {
            this.newGroupSection = !this.newGroupSection ;
            this.isMenuBar = false; 
        },

        openPrivateSection() {
            this.newPrivateSection = !this.newPrivateSection ;
            this.isMenuBar = false; 
        
        },

       
    },

    mounted(){
        
    }

    
    

}
</script>

<style>

</style>