<template>
    
    <!-- selected group details -->
    <div v-if="isGroupDetails" class="absolute h-full w-full flex justify-center items-center z-[1] inset-0 bg-black bg-opacity-60 "> 
        <div class="bg-gray-200 p-4 inset-0 rounded-xl min-w-72"> 
            <div class="flex items-center space-x-2 justify-between mb-3 "> 
                <img :src="`storage/${selectedGroup.group_avatar}`" alt="Profile" class="w-10 h-10 rounded-full object-cover border-2 border-white " />
                <h1 class="text-lg font-semibold">{{ selectedGrp.name }}</h1>
                <svg @click="isGroupDetails = false" class="cursor-pointer" xmlns="http://www.w3.org/2000/svg" height="22px" viewBox="0 -960 960 960" width="22px" fill="black">
                    <path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z" />
                </svg>
            </div>
            <hr class="border-black border-b mb-3"/>
            <div v-for="grpMembers in selectedGrp.members" :key="grpMembers.id" class="max-h-[40vh] overflow-y-auto"> 
                <div class="flex items-center justify-between cursor-pointer hover:bg-slate-400 py-2 mb-2 rounded-lg ">
                    <div class="flex items-center justify-around w-full"> 
                        <div class="flex items-center"> 
                            <img src="https://flowbite.com/application-ui/demo/images/users/roberta-casas.png" alt="user_avatar" class="w-8 h-8 rounded-full"/>
                            <h2 class="text-base font-bold ml-2">{{ grpMembers.user_name }}</h2>
                        </div>
                        <h2 class="text-sm ml-2">{{ grpMembers.role }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col h-[90vh]">
        <!-- Header -->
        <div class="bg-blue-600 text-white p-3 shadow-lg">
            <div class="max-w-4xl mx-auto flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <img :src="`storage/${selectedGroup.group_avatar}`" alt="Profile" class="w-10 h-10 rounded-full object-cover border-2 border-white" />
                    <div>
                        <h1 class="font-bold">{{ selectedGroup.name }} </h1>
                        <span class="text-sm mt-3"></span>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <button @click="groupDetails(selectedGroup.id)" class="p-2 hover:bg-blue-700 rounded-full transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"> 
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0110-2 1 1 0 010 2z"  />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Chat Messages -->
        <div ref="chatContainer" class="flex-1 p-4 chat-container overflow-y-auto max-h-full">
            <div v-for="(messages, date) in MessageData" :key="date" class="max-w-4xl mx-auto space-y-4">
                
                <div class="text-center my-2">
                    <span class="text-gray-500 text-sm bg-gray-200 rounded-full px-4 py-1">{{ date }}</span>
                </div>

                <div v-for="msg in messages" :key="msg.id" class="max-w-4xl mx-auto space-y-4"> 
                    <!-- Receive Message -->
                    
                    <div v-if="msg.sender_id === $page.props.auth.user.id" class="flex items-start justify-end space-x-2">
                        <div class="flex flex-col items-end">
                            <div class="bg-green-100 text-black rounded-lg rounded-tr-none p-2 shadow-md max-w-md flex items-center ">

                                <div v-if="msg.file_path || msg.file?.file_path"> 
                                    <p class="text-gray-500 text-left text-xs message-time mr-1 ml-2">
                                        {{ msg.file_name || msg.file?.file_name }}
                                    </p>

                                    <!-- Image Preview -->
                                    <img v-if="isImage(msg.file_path || msg.file?.file_path)"    :src="`/storage/${msg.file_path || msg.file?.file_path}`"   alt="Attachment"  class="w-36 h-36 rounded-lg object-cover border-2 mt-2 border-white" />

                                    <!-- Downloadable File Link -->
                                    <a v-else :href="`/storage/${msg.file_path || msg.file?.file_path}`"    class="text-blue-500 underline text-xs mt-2 ml-2"   download>    Download File </a>

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

                                <p class="text-gray-500 text-xs message-time mr-1 ml-2 text-right ">{{ formatTime(msg.created_at) }}</p> 
                            </div>
                           
                        </div>
                     
                    </div>

                    <!-- Sender Messages -->
                    <div v-else class="flex items-start space-x-2">
                        <img   :src="`storage/${msg.avatar ? msg.avatar : msg.user.avatar }`"  alt="Profile" class="w-8 h-8 rounded-full object-cover" />

                        <div class="flex flex-col items-start">
                            <div class="bg-white rounded-lg rounded-tl-none p-2 shadow-md max-w-md">
                                <div class="flex flex-wrap">
                                    <p class="text-xs text-green-800">{{ msg.sender_name ? msg.sender_name : msg.user.name }}</p>
                                   
                                    <p class="text-xs text-green-800 ml-3"> -- {{ msg.role ? msg.role : msg.user.role  }} -- </p>
                                </div>
                                
                                <div v-if="msg.file_path || msg.file?.file_path"> 
                                    <p class="text-gray-500 text-left text-xs message-time mr-1 ml-2">
                                        {{ msg.file_name || msg.file?.file_name }}
                                    </p>

                                        <!-- Image Preview -->
                                        <img v-if="isImage(msg.file_path || msg.file?.file_path)"  :src="`/storage/${msg.file_path || msg.file?.file_path}`"   alt="Attachment"     class="w-36 h-36 rounded-lg object-cover border-2 mt-2 border-white" />

                                        <!-- Downloadable file Link -->
                                        <a v-else :href="`/storage/${msg.file_path || msg.file?.file_path}`" 
                                        class="text-blue-500 underline text-xs mt-2 ml-2" 
                                        download>    Download File </a>

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

        <!-- Chat Input  -->
        <div class="bg-white border-t p-4 sticky bottom-0 w-full">
            <form @submit.prevent="newMessage" class="max-w-4xl mx-auto flex items-center space-x-4">
                <label class="p-2 text-gray-500 hover:text-gray-700 transition cursor-pointer relative inline-flex items-center"> 
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                    </svg>
                    <input @change="handleFileUpload"  type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
                </label>

                <input v-model="form.content" type="text" placeholder="Type your message..." class="flex-1 p-2 border rounded-full focus:outline-none focus:border-blue-500" />

                <button type="submit" class="p-2 text-white bg-blue-600 rounded-full hover:bg-blue-700 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</template>

  
  <script>
  import { mapGetters } from 'vuex';
  import axios from 'axios';
  export default {

    data(){
        return{
            form : {
                content : '',
                file : null
            },
            MessageData: [],
            isGroupDetails : false,
            selectedGrp : []
        }
    },
    computed: {
        ...mapGetters(['selectedGroup', "authUser"]),
    },

    watch: {
            selectedGroup: {
            handler(newVal) {
                if (newVal) {
                    this.getChatMessages();
                }
            },
            immediate: true 
        }
    },

    methods:{

        handleFileUpload(event) {
            this.form.file = event.target.files[0];
            console.log('event',event.target.files[0])
        },


        async newMessage(){

            if(!this.form.content && !this.form.file){
                alert("Please enter message !!!");
                return;
            }

            const formData = new FormData();
            formData.append('chat_id',this.selectedGroup.id); 
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
                
            const response = await axios.post("group-message", formData, {
                headers: { "Content-Type": "multipart/form-data" }
            });


                const newMessage = response.data.data;

                const date = new Date(newMessage.created_at).toLocaleDateString();

                if (!this.MessageData[date]) {
                    this.MessageData, date = [];
                }

                this.MessageData[date].push(newMessage);

                this.form.content = '';
                this.scrollToBottom();

            }catch(err){
                console.error(err);
                alert("Error creating group");
            }


        },

        // format file sies 
        formatFileSize(bytes) {
            if (bytes === 0) return "0 Bytes";
                const sizes = ["Bytes", "KB", "MB", "GB", "TB"];
                const i = Math.floor(Math.log(bytes) / Math.log(1024));
            return (bytes / Math.pow(1024, i)).toFixed(2) + " " + sizes[i];
        },  


        isImage(filePath) {
            return filePath ? /\.(jpg|jpeg|png|gif|svg)$/i.test(filePath) : false;
        },

        getChatMessages(){

            axios.get(`messages/group/${this.selectedGroup.id}`).then((result)=>{
             
                this.MessageData = this.groupMessagesByDate(result.data.messages);

                this.$nextTick(() => {
                    this.scrollToBottom();
                });

            }).catch((err)=>{
                console.log(err)
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
        
        // scroll bottom the page when page is load
        scrollToBottom() {
            this.$nextTick(() => {
                const container = this.$refs.chatContainer;
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
            });
        },

        groupDetails(grpId){
           
            this.isGroupDetails = !this.isGroupDetails

            axios.get(`group/${grpId}`).then((result)=>{
                console.log('selectedGrp 11')

                this.selectedGrp = result.data.data
                
            }).catch((err)=>{
                console.log(err)
            })
        },


        listenForMessages() {
        Echo.private(`chat.${this.selectedGroup.id}`)
            .listen("SendGroupMessage", (e) => {
              
                const date = new Date(e.created_at).toLocaleDateString();

            if (!this.MessageData[date]) {
                this.$set(this.MessageData, date, []);
            }
            this.MessageData[date].push(e);
            this.scrollToBottom();
        });
    },

    },
    mounted(){
        this.getChatMessages();

        this.listenForMessages();


    }
  }
  </script>
  
  <style>
  
  </style>