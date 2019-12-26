<template>
    <div style="width: 100%; height: 100vh;">
        <v-row justify="center" align="center" style="height: 100vh;">
            <v-progress-circular
                indeterminate
                size="65"
                color="primary"
            ></v-progress-circular>
        </v-row>
    </div>
</template>

<script>
    import showToast from "../../utils/Toast";

    export default {
        data() {
            return {
                token: localStorage.getItem('token') || '',
            }
        },
        async mounted() {
            if (this.$store.getters.isLoggedIn) {
                return;
            }
            if (this.$store.getters.hasToken) {
                const response = await this.$store.dispatch('login', {token: this.token});
                if (response.error) {
                    showToast(response.error, '', 'error');
                    await this.$router.push('/login');
                    return;
                }
                await this.$router.push('/clients');
            }
        }
    }
</script>

<style scoped>

</style>
