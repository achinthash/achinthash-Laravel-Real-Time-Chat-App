<template>

    <div v-for="grpList in filteredGroup" :key="grpList.id"> 

        <div  @click="selectGroup(grpList)"  class="p-4 bg-gray-100 mb-[2px] hover:bg-gray-200  cursor-pointer ">
            <div class="flex  items-center space-x-4">
                <img src="https://flowbite.com/application-ui/demo/images/users/roberta-casas.png" alt="user_avatar" class="w-8 h-8 rounded-full"/>

                <div class="flex-1">
                    <div class="flex justify-between items-center">
                        <h2 class="text-base font-bold">{{ grpList.name }}</h2>
                        <p class="text-xs text-gray-500">{{ formatTime(grpList.latest_message_time) }}</p>
                    </div>

                    <!-- Display the latest received message for this chat (echo) -->
                    <div v-if="latestMessage(grpList.id)" class="text-sm text-blue-500">  
                        
                        <div v-if="latestMessage(grpList.id).file" class="flex  "> 
                            <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#0000FF"><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm0-80h560v-560H200v560Zm40-80h480L570-480 450-320l-90-120-120 160Zm-40 80v-560 560Z"/></svg>
                            
                            <span class="ml-1"> 
                                {{ latestMessage(grpList.id).file.file_name.length > 10 ? latestMessage(grpList.id).file.file_name.substring(0,10) + '...' :  latestMessage(grpList.id).file.file_name }}
                            </span>

                        </div>

                        <div v-else> 
                            {{ latestMessage(grpList.id).content }}
                        </div>
                    

                    </div>

                    <!-- If no new message, show the latest stored message (database) -->
                    <div v-else class="text-sm text-gray-500 mt-1">
                      
                        <div v-if="grpList.file_details" class="flex  "> 
                            <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#0000FF"><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm0-80h560v-560H200v560Zm40-80h480L570-480 450-320l-90-120-120 160Zm-40 80v-560 560Z"/></svg>
                            
                            <span class="ml-1"> 

                                {{ grpList.file_details.file_name.length > 10 ? grpList.file_details.file_name.substring(0,10) + '...' : grpList.file_details.file_name  }}
                              
                            </span>

                        </div>

                        <div v-else> 
                            {{ grpList.latest_message }}
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>
    

</template>

<script>
import axios from 'axios';
import { mapActions } from 'vuex';


export default {

    data(){
        return{
            groupList : [],
            newMessages : [],
            messageCount: []
           
        }
    },

    methods:{
        ...mapActions(['selectGroup']), // store selected grop details
        getAllgroups(){
            axios.get('/all-groups').then((result)=>{
             
                this.groupList = result.data;
                this.listenForMessages();

                    if (e.chat_id === chat.id) {
                        this.messageCount.push(e);
                    }


            })
            .catch((err)=>{
                console.log(err)
            })
        },
        formatTime(datetime) {
            if (!datetime) return "";
            return new Date(datetime).toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });
        },

        // calculate the unread message count
        unreadCount(chatId, oldCount){

            let echatCount = this.messageCount.filter(message => message.chat_id === chatId).length;
            if(oldCount > 0){
                return oldCount + echatCount;
            }
            else{
                return this.messageCount.filter(message => message.chat_id === chatId).length;
            }

        },
        latestMessage(chatId) {
            return this.newMessages.find(message => message.chat_id === chatId);
       
        },

        listenForMessages() {

            this.groupList.forEach((chat)=>{

                Echo.private(`chat.${chat.id}`)
                .listen("SendGroupMessage", (e) => {
                  
                        if (e.chat_id === chat.id) {
                          
                            this.newMessages = this.newMessages.filter(m => m.chat_id !== chat.id);
                            this.newMessages.push(e);
                        }

                         
                        // indert message unread count
                    if (e.chat_id === chat.id) {
                            this.messageCount.push(e);
                        }

    
                  
                 });
                
            })
        },

     
       
    },
    mounted(){
        
        this.getAllgroups();

        this.listenForMessages();

    },
    computed: {
        filteredGroup() {
           
            return this.groupList.filter(grp =>
                grp.members.some(member => member.user_id === window.authUser.id)
            );


        },
  },
}
</script>

<style>

</style>