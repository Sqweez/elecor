<template>
    <div>
        <v-card>
            <v-card-title>
                Заявки и обратная связь
            </v-card-title>
            <v-card-text>
                <div class="d-flex p-2">
                    <v-btn style="width: 300px;" depressed :color="currentSegment === 'orders' ? 'primary' : ''"
                           @click="currentSegment = 'orders'">Заявки
                    </v-btn>
                    <v-btn style="width: 300px;" depressed :color="currentSegment === 'feedback' ? 'primary' : ''"
                           @click="currentSegment = 'feedback'">Обратная связь
                    </v-btn>
                </div>
                <div class="p-4">
                    <OrderSegment v-if="currentSegment === 'orders'"/>
                    <FeedbackSegment v-else/>
                </div>

            </v-card-text>
        </v-card>
    </div>
</template>

<script>
    import FeedbackSegment from "@/components/Control/FeedbackSegment/FeedbackSegment";
    import OrderSegment from "@/components/Control/OrderSegment/OrderSegment";
    import ACTIONS from "../../../store/actions";
    export default {
        components: {
            FeedbackSegment, OrderSegment,
        },
        async mounted() {
            await this.$store.dispatch(ACTIONS.GET_ORDERS);
            await this.$store.dispatch(ACTIONS.GET_FEEDBACK);
        },
        data: () => ({
            search: '',
            currentSegment: 'orders',
        }),
        methods: {
            switchSegment() {
                this.currentSegment = this.currentSegment === 'orders' ? 'feedback' : 'orders';
            }
        }
    }
</script>

<style scoped>

</style>
