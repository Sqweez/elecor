<template>
    <v-card>
        <v-card-title>
            Акции
        </v-card-title>
        <v-card-text>
            <div class="d-flex p-2">
                <v-btn style="width: 300px;" depressed :color="currentSegment === 'stocks' ? 'primary' : ''"
                       @click="currentSegment = 'stocks'">Акции
                </v-btn>
            </div>
            <div class="p-4">
                <NewsSegment v-if="currentSegment === 'news'"/>
                <StocksSegment v-else/>
            </div>

        </v-card-text>
    </v-card>
</template>

<script>
    import NewsSegment from '@/components/Control/NewsSegment/NewsSegment'
    import StocksSegment from '@/components/Control/StocksSegment/StocksSegment'
    import ACTIONS from "../../../store/actions";
    export default {
        components: {
            NewsSegment, StocksSegment,
        },
        async mounted() {
            await this.$store.dispatch(ACTIONS.GET_STOCKS);
        },
        data: () => ({
            currentSegment: 'stocks'
        }),
        methods: {
            switchSegment() {
                this.currentSegment = this.currentSegment === 'news' ? 'stocks' : 'news';
            }
        }

    }
</script>

<style scoped>

</style>
