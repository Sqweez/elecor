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
            </div>
            <div class="p-4">
                <ContactsSegment v-if="currentSegment === 'contacts'"/>
                <ServiceSegment v-if="currentSegment === 'services'"/>
            </div>
        </v-card-text>
    </v-card>
</template>

<script>
    import ContactsSegment from "../../../components/Segments/ContactsSegment/ContactsSegment";
    import ServiceSegment from "../../../components/Segments/ServiceSegment/ServiceSegment";
    export default {
        components: {
          ContactsSegment, ServiceSegment
        },
        data: () => ({
            currentSegment: 'contacts',
        })
    }
</script>

<style scoped>

</style>
