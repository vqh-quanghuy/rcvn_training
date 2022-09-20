<template>
    <v-app>
        <v-row>
            <v-card
                class="mx-auto"
                max-width="448"
            >
                
                <v-app-bar
                    color="primary"
                    density="compact"
                >
                    <template v-slot:prepend>
                    <v-app-bar-nav-icon></v-app-bar-nav-icon>
                    </template>

                    <v-app-bar-title>Photos</v-app-bar-title>

                    <template v-slot:append>
                        <v-btn icon="mdi-logout"></v-btn>
                    </template>
                </v-app-bar>
            </v-card>
        </v-row>
        <Login v-if="access_token === ''" v-on:login-success="onLoginSuccess"/>
        <UserList :access_token="access_token" :user="user_info" v-else />
    </v-app>
</template>

<script>
import Login from './components/Login.vue'
import UserList from './components/UserList.vue'

export default {
    name: 'App',
    data() {
        return {
            access_token: '',
            user_info: ''
        }
    },
    methods: {
        onLoginSuccess(data) {
            this.access_token = data.access_token;
            this.user_info = data.user;
        }
    },
    components: {
        Login,
        UserList
    }
}
</script>