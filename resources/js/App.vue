<template>
    <v-app>
        <component :is="layout" v-if="(hasToken && isLoggedIn) || layout === 'loginLayout'"/>
        <div style="width: 100%; height: 100vh; background-color: #dadada; position: absolute; z-index: 1" v-else>
            <v-row justify="center" align="center" style="height: 100vh;">
                <v-progress-circular
                    indeterminate
                    size="65"
                    color="primary"
                ></v-progress-circular>
            </v-row>
        </div>
    </v-app>
</template>

<script>
    import Navbar from "./components/Navigation/Navbar/Navbar";
    import MainLayout from "./layouts/Main/MainLayout";
    import LoginLayout from "./layouts/Login/LoginLayout";
    import ACTIONS from "./store/actions";
    import showToast from "./utils/Toast";

    String.prototype.replaceAll = function (search, replacement) {
        let target = this;
        return target.split(search).join(replacement);
    };
    export default {
        data: () => ({
            token: localStorage.getItem('token') || '',
        }),
        computed: {
            layout() {

                return (this.$route.meta.layout || 'Main') + 'Layout';
            },
            isLoggedIn() {
                return this.$store.getters.isLoggedIn;
            },
            hasToken() {
                return this.$store.getters.hasToken;
            }
        },
        async mounted() {
            if (this.$store.getters.isLoggedIn) {
                return;
            }
            if (this.$store.getters.hasToken) {
                const response = await this.$store.dispatch('login', {token: this.token});
                console.log(this.isLoggedIn);
                console.log(this.hasToken);
                if (response.error) {
                    showToast(response.error, '', 'error');
                    await this.$router.push('/login');
                    return;
                }
            }
        },
        components: {Navbar, MainLayout, LoginLayout}
    };
</script>

<style lang="scss">
    @import "styles/app";
</style>
