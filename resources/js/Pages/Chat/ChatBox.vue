<template>
    <div>
        
        <div v-if="selectedGroup">
           <GroupChatBox />
        </div>
        <div v-else-if="selectedPrivate">
           <PrivateChatBox />
        </div>

        <div v-else>
            <DefaultChatBox />
        </div>

    </div>
</template>

<script>
import { mapGetters } from 'vuex';
import DefaultChatBox from './DefaultChatBox.vue';
import GroupChatBox from './GroupChatBox.vue';
import PrivateChatBox from './PrivateChatBox.vue';

export default {
    computed: {
        ...mapGetters(['selectedGroup', 'selectedPrivate']), 
    },

    components:{
        DefaultChatBox,
        GroupChatBox,
        PrivateChatBox
    },
   


};
</script>
