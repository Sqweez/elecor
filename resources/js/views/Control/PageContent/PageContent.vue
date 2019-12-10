<template>
    <v-card>
        <v-card-title>Наполнение страниц</v-card-title>
        <v-card-text>
            <div class="d-flex p-2">
                <v-btn style="width: 300px;" depressed :color="currentSegment === 'contacts' ? 'primary' : ''"
                       @click="currentSegment = 'contacts'">Контакты
                </v-btn>
                <v-btn style="width: 300px;" depressed :color="currentSegment === 'services' ? 'primary' : ''"
                       @click="currentSegment = 'services'">Услуги
                </v-btn>
                <v-btn style="width: 300px;" depressed :color="currentSegment === 'banners' ? 'primary' : ''"
                       @click="currentSegment = 'banners'">Баннеры
                </v-btn>
            </div>
            <div class="p-4">
                <ContactsSegment v-if="currentSegment === 'contacts'"/>
                <ServiceSegment v-if="currentSegment === 'services'"/>
                <StocksSegment v-if="currentSegment === 'banners'"/>
            </div>
        </v-card-text>
    </v-card>
</template>

<script>
    import ContactsSegment from "../../../components/Segments/ContactsSegment/ContactsSegment";
    import ServiceSegment from "../../../components/Segments/ServiceSegment/ServiceSegment";
    import StocksSegment from "../../../components/Control/StocksSegment/StocksSegment";
    import ACTIONS from "../../../store/actions";
    export default {
        components: {
          ContactsSegment, ServiceSegment, StocksSegment
        },
        data: () => ({
            currentSegment: 'contacts',
        }),
        async mounted() {
            await this.$store.dispatch(ACTIONS.GET_STOCKS);
            await this.$store.dispatch(ACTIONS.GET_MOBILE_SERVICES);
        }
    }
</script>

<style scoped>

</style>
