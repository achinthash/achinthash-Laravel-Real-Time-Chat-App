<template>
  
    <div class="flex flex-col h-[90vh]">
        
        <!-- Header -->
        <div class="bg-blue-600 text-white p-3 shadow-lg">
            <div class="max-w-4xl mx-auto flex items-center justify-between">
                <div class="flex items-center space-x-4">
                <img  :src="`storage/${selectedPrivate.user_avatar ? selectedPrivate.user_avatar :UserDetails.avatar }`"  alt="Profile" class="w-10 h-10 rounded-full object-cover border-2 border-white" />
            
                <div>
           
                    <h1 class="font-bold">{{ selectedPrivate.user ?? UserDetails?.name ?? 'Unknown User' }} </h1>

                    <div v-if="checkUserOnlineStatus(UserDetails.user_id)">
                        <p>  online  </p>
                    </div>

                    <div v-else>
                        <p> offline </p>
                    </div>


                </div>
            </div>
               
            </div>
        </div>


        <!-- Chat Messages -->
        <div ref="chatContainer" class="flex-1 p-4 chat-container overflow-y-auto max-h-full">
        <div v-for="(message , date) in MessageData" :key="date"  class="max-w-4xl mx-auto space-y-4">


            <div className="text-center my-2">
            <span className="text-gray-500 text-sm bg-gray-200 rounded-full px-4 py-1">{{ date }}</span>
            </div>

           

            
            <div v-for="msg in message" :key="msg.id" class="max-w-4xl mx-auto space-y-4" > 
            
                <!-- Receive Message -->
                <div v-if="msg.sender_id == $page.props.auth.user.id " class="flex items-start justify-end space-x-2">
                    
                    <div class="flex flex-col items-end">
                        
                        <div class="bg-green-100 text-black rounded-lg rounded-tr-none p-2 shadow-md max-w-md flex items-center justify-center ">
                                       
                           

                            <div v-if="msg.file_path || msg.file?.file_path"> 
                                <p class="text-gray-500 text-left text-xs message-time mr-1 ml-2">
                                    {{ msg.file_name || msg.file?.file_name }}
                                </p>

                                    <!-- Image Preview -->
                                    <img v-if="isImage(msg.file_path || msg.file?.file_path)"  :src="`/storage/${msg.file_path || msg.file?.file_path}`"   alt="Attachment"  class="w-36 h-36 rounded-lg object-cover border-2 mt-2 border-white" />

                                    <!-- Downloadable File Link -->
                                    <a v-else :href="`/storage/${msg.file_path || msg.file?.file_path}`"  class="text-blue-500 underline text-xs mt-2 ml-2" 
                                    download> Download File </a>

                                    <div class="flex justify-between mt-2"> 
                                        <p class="text-gray-500 text-xs message-time ml-2">
                                            {{ formatFileSize(msg.file?.file_size ?? msg.file_size) }}
                                        </p>

                                        <p class="text-gray-500 text-xs message-time mr-2">
                                            {{ formatTime(msg.created_at) }}
                                        </p>

                                    </div>
                                </div>

                            <!-- Text Message -->
                            <div v-else class="flex items-center"> 
                                <p>{{ msg.content }}</p>

                                <p class="text-gray-500 text-xs message-time mr-1 ml-2">   {{ formatTime(msg.created_at) }} </p> 
                            </div>  

                            <div v-if="msg.status == 'read'"> 
                               
                                <svg class="ml-2" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#008000"><path d="M268-240 42-466l57-56 170 170 56 56-57 56Zm226 0L268-466l56-57 170 170 368-368 56 57-424 424Zm0-226-57-56 198-198 57 56-198 198Z"/></svg>

                            </div>

                            
                        </div>
                        
                    </div>
                    <img  :src="`/storage/${$page.props.auth.user.avatar}`"  alt="Profile"  class="w-8 h-8 rounded-full object-cover" />
                </div>
            
                <!-- Sender Messages -->
                <div  v-else class="flex items-start  space-x-2">

                    <img  :src="`storage/${selectedPrivate.user_avatar ? selectedPrivate.user_avatar :UserDetails.avatar }`"  alt="Profile"  class="w-8 h-8 rounded-full object-cover" />

                    <div class="flex flex-col items-start">
                        <div class="bg-white rounded-lg rounded-tl-none p-2 shadow-md max-w-md flex items-center" >
                        
                            
                            <div v-if="msg.file_path || msg.file?.file_path"> 
                                <p class="text-gray-500 text-left text-xs message-time mr-1 ml-2">
                                    {{ msg.file_name || msg.file?.file_name }}
                                </p>

                                    <!-- Image Preview -->
                                    <img v-if="isImage(msg.file_path || msg.file?.file_path)" :src="`/storage/${msg.file_path || msg.file?.file_path}`"      alt="Attachment"    class="w-36 h-36 rounded-lg object-cover border-2 mt-2 border-white" />

                                    <!-- Downloadable File Link -->
                                    <a v-else :href="`/storage/${msg.file_path || msg.file?.file_path}`"  class="text-blue-500 underline text-xs mt-2 ml-2" 
                                    download> Download File </a>

                                    <div class="flex justify-between mt-2"> 
                                        <p class="text-gray-500 text-xs message-time ml-2">
                                            {{ formatFileSize(msg.file?.file_size ?? msg.file_size) }}
                                        </p>

                                        <p class="text-gray-500 text-xs message-time mr-2">
                                            {{ formatTime(msg.created_at) }}
                                        </p>

                                    </div>
                                </div>

                            <!-- Text Message -->
                            <div v-else class="flex items-center"> 
                                <p>{{ msg.content }}</p>
                                <p class="text-gray-500 text-xs message-time mr-1 ml-2">
                                    {{ formatTime(msg.created_at) }}
                                </p> 
                            </div>  

                        </div>
                       
                    </div>
                    
                </div>
            </div>



        </div>
        </div>
      
        <!-- Chat Input - Always at Bottom -->
        <div class="bg-white border-t p-4 sticky bottom-0 w-full">
            <form @submit.prevent="newMessage" class="max-w-4xl mx-auto flex items-center space-x-4">
                


                <label class="p-2 text-gray-500 hover:text-gray-700 transition cursor-pointer relative inline-flex items-center"> 
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round"  stroke-linejoin="round"  stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                    </svg>
                    <input   @change="handleFileUpload"   type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"   />
                </label>

                <input  v-model="form.content"  type="text"  placeholder="Type your message..."  class="flex-1 p-2 border rounded-full focus:outline-none focus:border-blue-500" />
           
                
                <button type="submit" class="p-2 text-white bg-blue-600 rounded-full hover:bg-blue-700 transition">
               <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"  stroke-width="2"  d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"  />
                </svg>
                </button>

            
            </form>
        </div>
    </div>

  
</template>

<script>

import { mapGetters } from 'vuex';
import axios from 'axios';
import { useOnlineUsersStore } from '@/onlineUsersStore';


export default {

    data(){
        return{
            form: {
                content : '',
                file : null
            },
            MessageData: {},
            UserDetails : [],
            isSelectedUserOnline : '',
            onlineUsers: [], 

            onlineUsersStore : useOnlineUsersStore()
        }
    },

    watch: {
        selectedPrivate: {
            
            handler(newVal) {
             
                if (newVal) {
          
                    this.getPrivateChats();

                    this.getPrivateChatsUserDetails();
         
                }
            },
            immediate: true, 

        },

    },


    methods:{

        listenForMessages() {
        Echo.private(`chat.${this.selectedPrivate.id}`)
            .listen("SendMessage", (e) => {
        
                const date = new Date(e.created_at).toLocaleDateString();
            if (!this.MessageData[date]) {
                this.$set(this.MessageData, date, []);
            }
            this.MessageData[date].push(e);
            this.scrollToBottom();
            });
        },

        async markMessagesAsRead() {
            
            const loggedInUserId = this.$page.props.auth.user.id;

            const unreadMessages = Object.values(this.MessageData)
                .flat()
                .filter(msg => msg.status === 'unread' && msg.sender_id !== loggedInUserId  && msg.chat_id == this.selectedPrivate.id );

            if (unreadMessages.length === 0) return;

            const messageIds = unreadMessages.map(msg => msg.id);

            try {
                await axios.put(`/messages/read`, { message_ids: messageIds });

                unreadMessages.forEach(msg => msg.status = "read");

            } catch (error) {
                console.error("Error updating message status:", error);
            }
        },


        getPrivateChatsUserDetails(){
            axios.get(`/private-chat-user/${this.selectedPrivate.id}`).then((response)=>{
              
                this.UserDetails = response.data;
          
            })
            .catch((err)=>{
                console.log(err);
            })
        },

        isImage(filePath) {
            return filePath ? /\.(jpg|jpeg|png|gif|svg)$/i.test(filePath) : false;
        },

        getPrivateChats(){
            axios.get(`/private-messages/${this.selectedPrivate.id}`).then((response)=>{
               
                this.MessageData = this.groupMessagesByDate(response.data);

                 // scroll bottom when fetch data
                 this.$nextTick(() => {
                    this.scrollToBottom();
                });

               
            // this.$nextTick(() => {
            //     this.markMessagesAsRead();
            // });

            this.markMessagesAsRead();
            })
            .catch((err)=>{
                console.error(err);
            })
        },

        groupMessagesByDate(messages) {
            const groupedMessages = {};
            
            messages.forEach(msg =>{
                const date = new Date(msg.created_at).toLocaleDateString();

                if(!groupedMessages[date]){
                    groupedMessages[date] = [];
                }
                groupedMessages[date].push(msg);
            })

            return groupedMessages;
        },
        formatTime(datetime) {
            if (!datetime) return "";
                return new Date(datetime).toLocaleTimeString([], { 
                    hour: "2-digit", 
                    minute: "2-digit", 
                    hour12: false 
                });    
        },


        scrollToBottom() {
            this.$nextTick(() => {
                const container = this.$refs.chatContainer;
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
            });
        },

        formatFileSize(bytes) {
            if (bytes === 0) return "0 Bytes";
            const sizes = ["Bytes", "KB", "MB", "GB", "TB"];
            const i = Math.floor(Math.log(bytes) / Math.log(1024));
            return (bytes / Math.pow(1024, i)).toFixed(2) + " " + sizes[i];
        },


        handleFileUpload(event) {
            this.form.file = event.target.files[0];
         
        },


        async newMessage(){
            if(!this.form.content && !this.form.file){
                alert("Please enter message !!!");
                return;
            }

            const formData = new FormData();

            formData.append('chat_id',  this.selectedPrivate.id); 
            formData.append('status', 'unread');

              // if message send with file
            if (this.form.file) {
                formData.append("file", this.form.file);
                formData.append('content',  '-');
                formData.append('message_type','file');
            }
            else{

                formData.append('content', this.form.content);
                formData.append('message_type','text');
            }

            try{

                const response = await axios.post("private-messages" , formData,{
                    headers: { "Content-Type": "multipart/form-data" }
                })

                const newMessage = response.data.data;

                const date = new Date(newMessage.created_at).toLocaleDateString();

                if(!this.MessageData[date]){
                    this.MessageData[date] =  [];
                }

                this.MessageData[date].push(newMessage); 

                this.form.content = '';
                this.scrollToBottom();

            }
            catch(err){
                console.error(err);
                alert("Error creating group 11");
            }

        },
      
        checkUserOnlineStatus(userId) {
            return this.onlineUsersStore.onlineUsers.some(user => user.id === userId);
        },

    },


    computed: {
        ...mapGetters([ "selectedPrivate"]),
    },

    mounted(){

        this.getPrivateChats();

        this.listenForMessages();

        if(!this.selectedPrivate.user){
            this.getPrivateChatsUserDetails();
        }

        this.markMessagesAsRead();
            
    }

  

    
}
</script>

<style>

</style>