<template>
    <div>
        <Navbar/>
        <div class="container">
            <router-view></router-view>
        </div>
    </div>
</template>

<script>
    import Navbar from "../../components/Navigation/Navbar/Navbar";
    import ACTIONS from "../../store/actions";

    export default {
        created() {
          this.$store.dispatch(ACTIONS.INIT);
        },
        components: {
            Navbar
        }
    }
</script>

<style scoped lang="scss">
    @import "style";
</style>
