<template>

</template>
<script>
export default {
    name: 'UserList',
    props: ['access_token', 'user'],
    data() {
        return {
            users: []
        }
    },
    methods: {
        async load() {
            await this.axios
            .get('api/user/users/', {
                headers: {
                    Accept: 'application/json',
                    Authorization: 'Bearer ' + this.access_token
                }
            })
            .then(res => {
                if (res.status === 200) {
                    const DATA = res.data.data;
                    this.users = DATA.data;
                    console.log('this.users :>> ', this.users);
                    // console.log('res :>> ', res);
                }
            })
            .catch(err => {
                if (err.status !== 200) console.error(err.response.data.message);
            })
        }
    },
    mounted() {
        this.load();
    }
}
</script>