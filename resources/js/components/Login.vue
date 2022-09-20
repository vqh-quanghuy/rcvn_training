<template>
    <v-row justify="center">
        <v-col md="4">
            <v-card class="elevation-12">
                <v-card-title>Login form</v-card-title>
                <v-card-text>
                    <v-form>
                        <v-text-field
                            v-model="email"
                            prepend-icon="mdi-account-circle"
                            name="email"
                            label="Email"
                            type="text"
                        ></v-text-field>
                        <v-text-field
                            v-model="password"
                            id="password"
                            prepend-icon="mdi-lock"
                            name="password"
                            label="Password"
                            type="password"
                        ></v-text-field>
                    </v-form>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="primary" @click="loginSubmit()">Login</v-btn>
                </v-card-actions>
            </v-card>
        </v-col>
    </v-row>
</template>

<script>
export default {
    name: 'Login',
    data() {
        return {
            email: '',
            password: '',
        }
    },
    methods: {
        async loginSubmit() {
            await this.axios
            .post('api/user/auth/login', {
                email: this.email,
                password: this.password
            })
            .then(res => {
                if (res.status === 200) {
                    let access_token = res.data.access_token;
                    let user_info = res.data.user;

                    this.$emit('login-success', { access_token: access_token, user: user_info})
                }
            })
            .catch(err => {
                if (err.status === 422) console.error(err.response.data.message);
            })
        }
    },
};
</script>

<style></style>
