<template>
    <v-app>
        <component :is="layout"/>
    </v-app>
</template>

<script>
    import Navbar from "./components/Navigation/Navbar/Navbar";
    import MainLayout from "./layouts/Main/MainLayout";
    import LoginLayout from "./layouts/Login/LoginLayout";
    import ACTIONS from "./store/actions";

    String.prototype.replaceAll = function (search, replacement) {
        let target = this;
        return target.split(search).join(replacement);
    };
    export default {
        data() {
            return {}
        },
        computed: {
            layout() {
                return (this.$route.meta.layout || 'Main') + 'Layout';
            }
        },
        components: {Navbar, MainLayout, LoginLayout}
    };
</script>

<style lang="scss">
    @import "styles/app";
</style>
