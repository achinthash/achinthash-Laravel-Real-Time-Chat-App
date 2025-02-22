<template>
   <div class="bg-gray-200 p-4 inset-0 rounded-xl min-w-72"> 

        <div class="flex items-center space-x-2 justify-between mb-3"> 
            <h1 class="text-lg font-semibold">Create new group</h1>
            <svg @click="$emit('CloseNewGroup')" xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 -960 960 960" width="18px" fill="black">
                <path
                d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z" />
            </svg>
        </div>

        <div class=" mb-1 border-b-[1px] border-black">
            <input  v-model="searchQuery"  type="text"   placeholder="Search for Contacts"  class="py-2 px-2 border-1  rounded-xl w-full mb-2"/>

        </div>


        

        <div v-for="users in filteredUsers" :key="users.id" class="max-h-[40vh] overflow-y-auto"> 
            
            <div @click="GroupUserSelect(users.id)"  class="flex items-center justify-between cursor-pointer  hover:bg-slate-400  py-2 mb-2 rounded-lg ">
                
                <div  class="flex items-center "> 
                    <img src="https://flowbite.com/application-ui/demo/images/users/roberta-casas.png" alt="user_avatar" class="w-8 h-8 rounded-full"/>
                    <h2 class="text-base font-bold ml-2">{{ users.name }}</h2>
                </div>
                <svg v-if="groupUserSelected.includes(users.id) " xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"    fill="#000000"><path d="m424-296 282-282-56-56-226 226-114-114-56 56 170 170Zm56 216q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/>
                </svg>
            </div>

          


        </div>
            
       
        <form @submit.prevent="createGroup" v-if="isGroupCreateSection" class=" mb-1 flex flex-col  border-t-[1px] border-black">
            
            <label class="text-sm mt-2 ml-2">Group Name</label>
            <input v-model="form.name"  type="text"   placeholder="Group name(optional)"  class="py-2 px-2 border-1  rounded-xl w-full mb-2" />
            
            <label class="text-sm ml-2" > Avatar</label>
            <input  @change="handleFileUpload"  type="file"   placeholder="Group zzname(optional)"  class="py-2 px-2 border-1 bg-white border rounded-xl w-full mb-2" />
            
            <button class="bg-blue-500 p-2 flex-nowrap text-sm rounded-lg text-white font-bold mt-2 ">Create</button>
        
        </form>

    </div>
</template>

<script>
import axios from 'axios';
import { useForm } from '@inertiajs/vue3';


export default {

    data(){
        return{

            isgroupUserSelected: false,
            groupUserSelected : [],
            userList: [],
            searchQuery : '',
            isGroupCreateSection : false,
            form: {
                name: '',
                group_avatar: null,
            }
        }
    },
    methods:{


        GroupUserSelect(userId) {
            if (!window.authUser?.id || userId === window.authUser.id) {
                return;
            }

            const index = this.groupUserSelected.indexOf(userId);
            if (index === -1) {
                this.groupUserSelected.push(userId);
            } else {
                this.groupUserSelected.splice(index, 1);
            }

            this.isGroupCreateSection = this.groupUserSelected.length > 0;


        },


        getUsersAll(){
            axios.get('/all-users').then((result)=>{
                this.userList = result.data;
            })
            .catch((err)=>{
                console.log(err)
            })
        },
        handleFileUpload(event) {
            this.form.group_avatar = event.target.files[0];
        },

        async createGroup() {
            if (this.groupUserSelected.length === 0) {
                alert("Please select at least one user.");
                return;
            }

            const formData = new FormData();
            formData.append("type", "group");
            formData.append("name", this.form.name);
            formData.append("created_by", window.authUser.id);
          
            this.groupUserSelected.forEach((userId) => {
                formData.append("users[]", userId);
            });


            if (this.form.group_avatar) {
                formData.append("group_avatar", this.form.group_avatar);
            }

            try {
                const response = await axios.post("new-group", formData, {
                    headers: { "Content-Type": "multipart/form-data" }
                });

                alert(response.data.message);
                this.$emit("CloseNewGroup");
            } catch (error) {
                console.error(error);
                alert("Error creating group");
            }
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

        if (window.authUser?.id) {
            this.groupUserSelected.push(window.authUser.id);
        }
    }
}
</script>

<style>

</style>