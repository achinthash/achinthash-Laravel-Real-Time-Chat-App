<template>

        <div class="border-b-2 py-2 px-2">
            <input  v-model="searchQuery"  type="text"   placeholder="Search for Contacts"  class="py-2 px-2 border-1  rounded-xl w-full"/>
        </div>


    <div v-for="chat in filteredUsers" :key="chat.id" class="p-4 bg-gray-100 mb-[2px]">
        <div @click="conversation(chat)" class="flex items-center space-x-4 cursor-pointer">
        
            <div className="relative w-10 h-10">
                <img src="https://flowbite.com/application-ui/demo/images/users/roberta-casas.png" alt="User Avatar"  className="w-10 h-10 rounded-full border-2"/>
                <span v-if="isUserOnline(chat.user)"  className="absolute bottom-0 right-0 w-4 h-4 bg-green-500 border-2  rounded-full"></span>
                <span v-else  className="absolute bottom-0 right-0 w-4 h-4 bg-red-500 border-2  rounded-full"></span>

            </div>

            <div class="flex-1 ">
                <div class="flex justify-between items-center">
                    <h2 class="text-base font-bold">{{ chat.user }}</h2>
                    <p class="text-xs text-gray-500">{{ formatTime(chat.latest_message.created_at) }}</p>
                </div> 
            
                <div class="flex justify-between"> 
                    

                    <!-- Display the latest received message for this chat (echo) -->
                    <div v-if="latestMessage(chat.id)" class="text-sm text-blue-500">  
                        
                        <div v-if="latestMessage(chat.id).file" class="flex justify-between "> 
                            <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#0000FF"><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm0-80h560v-560H200v560Zm40-80h480L570-480 450-320l-90-120-120 160Zm-40 80v-560 560Z"/></svg>
                            
                            <span class="ml-1"> 
                                {{ latestMessage(chat.id).file.file_name.length > 10 ? latestMessage(chat.id).file.file_name.substring(0,10) + '...' :  latestMessage(chat.id).file.file_name }}
                            </span>

                        </div>

                        <div v-else> 
                            {{ latestMessage(chat.id).content }}
                        </div>
                    

                    </div>

                    <!-- If no new message, show the latest stored message (database) -->
                    <div v-else class="text-sm text-gray-500 mt-1">
                        
                        <div v-if="chat.latest_message.file_details" class="flex justify-between "> 
                            <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#0000FF"><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm0-80h560v-560H200v560Zm40-80h480L570-480 450-320l-90-120-120 160Zm-40 80v-560 560Z"/></svg>
                            
                            <span class="ml-1"> 

                                {{ chat.latest_message.file_details.file_name.length > 10 ? chat.latest_message.file_details.file_name.substring(0,10) + '...' : chat.latest_message.file_details.file_name  }}
                              
                            </span>

                        </div>

                        <div v-else> 
                            {{ chat.latest_message.content }}
                        </div>

                    </div>

                      <!-- Unread message count -->
                    <div v-if="unreadCount(chat.id, chat.unread_count ) > 0" class="flex items-center justify-center w-6 h-6 text-white bg-red-500 rounded-full">
                        {{ unreadCount(chat.id, chat.unread_count) }}
                    </div>
                </div>

            </div> 
        </div>
    </div>

 
</template>

<script>
import axios from 'axios';
export default {
    data() {
        return {
            chatList: [],
            newMessages : [],
            messageCount: [],
            searchQuery: ''
        };
    },
    props: {
        onlineUsers: {
            type: Array,
            required: true
        }
    },
    methods: {
        getLatestChat() {

            axios.get('/private-chat-list')
                .then((result) => {
                    this.chatList = result.data;
                    this.listenForMessages();
                })
                .catch((err) => {
                    console.log(err);
                });
        },

        formatTime(datetime) {
            if (!datetime) return "";
            return new Date(datetime).toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });
        },

        conversation(chat) {

            this.$store.dispatch('selectChat', chat);

            this.messageCount = this.messageCount.filter(message => message.chat_id !== chat.id); 

        },
        isUserOnline(username) {
            return this.onlineUsers.some(user => user.name === username);
        },
        listenForMessages() {
           
            this.chatList.forEach((chat) => {
            
                Echo.private(`chat.${chat.id}`)
                .listen("SendMessage", (e) => {
                            
                // Add the latest message
                    if (e.chat_id === chat.id) {
                        this.newMessages = this.newMessages.filter(m => m.chat_id !== chat.id);
                        this.newMessages.push(e);
                    }

                //   message count 

                if (e.chat_id === chat.id) {
                        this.messageCount.push(e);
                    }
                });

            });

        },

       
        latestMessage(chatId) {
            return this.newMessages.find(message => message.chat_id === chatId);
        
        },


        unreadCount(chatId, oldCount) {

            let echatCount = this.messageCount.filter(message => message.chat_id === chatId).length;
            if(oldCount > 0){
                return oldCount + echatCount;
            }
            else{
                return this.messageCount.filter(message => message.chat_id === chatId).length;
            }
            
        }
    }, 
    mounted() {
        this.getLatestChat();
        this.listenForMessages();
    },
    computed:{
        filteredUsers(){
            return this.chatList.filter((users)=>
            users.user.toLowerCase().includes(this.searchQuery.toLowerCase()));
        }
    },
};
</script>
