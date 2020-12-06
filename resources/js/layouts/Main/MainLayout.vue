<template>
    <div v-if="token">
        <Navbar/>
        <div class="container">
            <v-overlay :value="isLoading">
                <v-progress-circular
                    indeterminate
                    size="64"
                    color="danger" />
            </v-overlay>
            <router-view></router-view>
        </div>
    </div>
</template>

<script>
    import Navbar from "../../components/Navigation/Navbar/Navbar";
    import ACTIONS from "../../store/actions";

    export default {
        data: () => ({
            token: localStorage.getItem('token') || ''
        }),
        async created() {
            if (this.$store.getters.isLoggedIn) {
                await this.$store.dispatch(ACTIONS.INIT);
            }
        },
        computed: {
            isLoading() {
                return this.$store.getters.IS_LOADING;
            }
        },
        components: {
            Navbar
        }
    }
</script>

<style scoped lang="scss">
    @import "style";
</style>
