<script setup>

import { ref, watch, computed, onMounted, onUnmounted } from 'vue';
import { useStore } from 'vuex'; // Import useStore for Vuex



import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useOnlineUsersStore } from '@/onlineUsersStore';
import UsersList from './UsersList.vue';
import ChatBox from './ChatBox.vue';


const onlineUsers = ref([]);
const onlineUsersStore = useOnlineUsersStore(); 


watch(onlineUsersStore.onlineUsers, (newUsers) => {
});

watch(onlineUsers, (newUsers) => {
});



// Function to update screen width on resize
const store = useStore(); 


const selectedGroup = computed(() => store.getters.selectedGroup);
const selectedPrivate = computed(() => store.getters.selectedPrivate);


const screenWidth = ref(window.innerWidth);


const showUsersList = computed(() =>{
    return screenWidth.value >= 775 || (!selectedPrivate.value && !selectedGroup.value) ;
})

const showChatBox = computed(() => {
    return screenWidth.value >= 775 ||(selectedPrivate.value || selectedGroup.value) ;
});


const updateWidth = () => {
    screenWidth.value = window.innerWidth;
};


onMounted(() =>{
    window.addEventListener('resize', updateWidth);
});


onUnmounted(() =>{
    window.removeEventListener('resize' , updateWidth);
});



// Echo event listeners for real-time updates

    Echo.join('online-users')
        .here((users) => {
            onlineUsers.value = users; // Update list when joining the channel
    

        onlineUsersStore.setOnlineUsers(users); // Update list when joining
        })
        .joining((user) => {
            if (!onlineUsers.value.some(u => u.id === user.id)) {

            onlineUsersStore.addUser(user); // Add new user
            }
        
        })
        .leaving((user) => {

            onlineUsers.value = onlineUsers.value.filter(u => u.id !== user.id); // Remove user
        

        onlineUsersStore.removeUser(user); // Remove user
        })
        .listen("UserOnlineStatus", (data) => {
      
    });

</script>

<style scoped>

.chatbox {
    background-image: url('/storage/images/backgroud-image.jpg');
    background-size: cover;
    background-position: center;
}


</style>

<template>
    <Head title="Chat" />

    <AuthenticatedLayout class="h-screen overflow-y-hidden">
        <div class="w-full bg-white h-[90vh] overflow-y-auto grid grid-cols-4">
            
            
           
            <div v-if="showUsersList" class="md:col-span-1 col-span-4">
                <UsersList :onlineUsers="onlineUsers" />
            </div>

            
            <div  v-if="showChatBox" class="col-span-4 md:col-span-3 chatbox">
                <ChatBox />
            </div>

        </div>

      
    </AuthenticatedLayout>
</template>
