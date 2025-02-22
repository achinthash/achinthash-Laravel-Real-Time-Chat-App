import { defineStore } from "pinia";
import { ref } from "vue";

export const useOnlineUsersStore = defineStore("onlineUsers", () => {
    const onlineUsers = ref([]);

    // Function to update users when joining the channel
    function setOnlineUsers(users) {
        onlineUsers.value = users;
    }

    // Function to add a new user
    function addUser(user) {
        if (!onlineUsers.value.some(u => u.id === user.id)) {
            onlineUsers.value.push(user);
        }
    }

    // Function to remove a user
    function removeUser(user) {
        onlineUsers.value = onlineUsers.value.filter(u => u.id !== user.id);
    }

    return { onlineUsers, setOnlineUsers, addUser, removeUser };
});
