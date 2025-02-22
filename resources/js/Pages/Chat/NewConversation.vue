<template>
    <div class="bg-gray-200 p-4 inset-0 rounded-xl min-w-72"> 

        <div class="flex items-center space-x-2 justify-between mb-3"> 
            <h1 class="text-lg font-semibold">Start new Conversation</h1>
            <svg @click="$emit('CloseNewConversation')" xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 -960 960 960" width="18px" fill="black">
                <path
                d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z" />
            </svg>
        </div>

        <div class=" mb-1 border-b-[1px] border-black">
            <input  v-model="searchQuery"  type="text"   placeholder="Search for Contacts"  class="py-2 px-2 border-1  rounded-xl w-full mb-2"/>

        </div>


        <div v-for="users in filteredUsers" :key="users.id" class="max-h-[40vh] overflow-y-auto"> 
            
            <div @click="selectUser(users)"  class="flex items-center justify-between cursor-pointer  hover:bg-slate-400  py-2 mb-2 rounded-lg ">
                
                <div  class="flex items-center "> 
                    <img :src="`storage/${users.avatar}`" alt="user_avatar" class="w-8 h-8 rounded-full"/>
                    <h2 class="text-base font-bold ml-2">{{ users.name }}  </h2>
                </div>
            
            </div>

        </div>
        

    </div>
</template>

<script>

import { mapActions } from 'vuex';
import PrivateChatBox from './PrivateChatBox.vue';


export default {
    data(){
        return{
            userList: [],
            searchQuery : '',
            selectedUser : null,
            
        }
    },
    components:{
        PrivateChatBox
    },
    methods:{
        ...mapActions(['selectGroup', 'selectChat']), 

        getUsersAll(){
            axios.get('/all-users').then((result)=>{
             
                this.userList = result.data;
            })
            .catch((err)=>{
                console.log(err)
            })
        },

        async newPrivateChat(){
            
            const formData = new FormData();
            formData.append('type', 'private');
            formData.append('created_by', window.authUser.id);


           const userIds = [this.selectedUser, window.authUser.id];
            userIds.forEach((id) => {
                formData.append('users[]', id);  
            });


            try{

                const response = await axios.post("new-private" , formData,{
                    headers: { "Content-Type": "multipart/form-data" }
            })
               
                this.$store.dispatch('selectChat', response.data.data);
                
            }
            catch(err){
                console.error(err);
                alert("Error creating group");
            }



        },
     
        selectUser(user) { 
  
        this.$emit("CloseNewConversation");
        this.selectedUser = user.id;
     
        this.newPrivateChat();

    }
     
    },
    computed:{
        filteredUsers(){
            return this.userList.filter((user)=>
            user.name.toLowerCase().includes(this.searchQuery.toLowerCase()));
        }
    },
    mounted(){
        this.getUsersAll();
    }

}
</script>

<style>

</style>