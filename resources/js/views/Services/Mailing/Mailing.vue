<template>
    <div>
        <v-card>
            <v-card-title>
                Рассылка
            </v-card-title>
            <v-card-text>
                <div class="p-3">
                    <v-btn
                        depressed
                        :color="currentSegment === 'mailing' ? 'primary' : ''"
                        @click="currentSegment = 'mailing'">
                        Рассылка
                    </v-btn>
                    <v-btn
                        depressed
                        :color="currentSegment === 'mailing_history' ? 'primary' : ''"
                        @click="currentSegment = 'mailing_history'">
                        История рассылок
                    </v-btn>
                    <v-btn
                        depressed
                        :color="currentSegment === 'mailing_template' ? 'primary' : ''"
                        @click="currentSegment = 'mailing_template'">
                        Шаблоны
                    </v-btn>
                    <MailingSegment v-if="currentSegment === 'mailing'"/>
                    <MailingHistorySegment v-if="currentSegment === 'mailing_history'"/>
                    <MailingTemplateSegment v-if="currentSegment === 'mailing_template'"/>
                </div>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
    import MailingTemplateSegment from "../../../components/Segments/MailingTemplateSegment/MailingTemplateSegment";
    import MailingSegment from "../../../components/Segments/MailingSegment/MailingSegment";
    import MailingHistorySegment from "../../../components/Segments/MailingHistorySegment/MailingHistorySegment";
    export default {
        components: {MailingHistorySegment, MailingSegment, MailingTemplateSegment},
        data: () => ({
            currentSegment: 'mailing'
        }),
        async mounted() {
            await this.$store.dispatch('getDebts');
            await this.$store.dispatch('getMailingTemplates');
        },
    }
</script>

<style scoped>

</style>
