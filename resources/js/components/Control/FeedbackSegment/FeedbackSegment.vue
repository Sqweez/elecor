<template>
    <div class="feedback-container">
        <v-btn color="primary" class="button-add" v-if="user.role_id === 3 || user.role_id === 4">
            Экспорт данных
            <v-icon>mdi-file-excel-box</v-icon>
        </v-btn>
        <v-spacer></v-spacer>
        <v-text-field
            v-model="search"
            clearable
            append-icon="mdi-account-search"
            label="Поиск"
            class="mb-2"
            single-line
            hide-details
        ></v-text-field>
        <v-data-table
            v-if="feedbacks"
            no-results-text="Нет результатов"
            no-data-text="Нет данных"
            :items="feedbacks"
            :footer-props="{
                                'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                                'items-per-page-text': 'Записей на странице',
                            }"
            :headers="feedbackHeaders"
            :search="search"
        >

            <template v-slot:item.feedback="{item}">
                <i>{{ item.feedback }}</i>
            </template>
            <template v-slot:item.message="{item}">
                <i>{{ item.message }}</i>
            </template>
            <template v-slot:item.action="{item}">
                <div v-if="item.is_worked">
                    <h3 class="green--text">Отработано</h3>
                </div>
                <div v-else>
                    <v-btn color="primary" @click="showModal(item)">Ответить
                        <v-icon>mdi-message</v-icon>
                    </v-btn>
                    <v-btn color="success" @click="currentFeedback = item; confirmModal = true;">
                        Обработано
                        <v-icon>mdi-check</v-icon>
                    </v-btn>
                </div>
            </template>
            <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
            </template>
        </v-data-table>
        <MessageModal
            :state="showMessageModal"
            v-on:modalClosed="closeMessageModal"
            v-on:sendMessage="sendMessage"/>
        <ConfirmationModal
            :state="confirmModal"
            message="Отметить данную заявку как обработанную?"
            v-on:cancel="confirmModal = false; currentFeedback = null"
            v-on:confirm="onConfirm"
        />
    </div>
</template>

<script>
    import MessageModal from "../../Modals/MessageModal/MessageModal";
    import {sendPushToClient} from "../../../api/client/clientApi";
    import ACTIONS from "../../../store/actions";
    import showToast from "../../../utils/Toast";
    import ConfirmationModal from "../../Modals/ConfirmationModal/ConfirmationModal";

    export default {
        components: {ConfirmationModal, MessageModal},
        computed: {
            feedbacks() {
                return this.$store.getters.feedback;
            },
            user() {
                return this.$store.getters.user;
            },
            feedbackHeaders() {
                const headers = [
                    {text: 'Контрагент', value: 'client', sortable: false},
                    {text: 'Дата', value: 'date', sortable: false},
                    {text: 'Обратная связь', value: 'feedback', sortable: false},
                    {text: 'Ответ', value: 'answer', sortable: false},
                ];

                if (this.user.role_id === 3 || this.user.role_id === 4) {
                    headers.push({text: 'Действие', value: 'action', sortable: false})
                }

                return headers;
            }
        },
        data: () => ({
            search: '',
            currentFeedback: null,
            showMessageModal: false,
            confirmModal: false,
        }),
        methods: {
            showModal(e) {
                this.currentFeedback = e;
                this.showMessageModal = true;
            },
            closeMessageModal() {
                this.showMessageModal = false;
                this.currentFeedback = null;
            },
            async sendMessage(e) {
                const push = {
                    title: e.title,
                    body: e.body,
                    client_id: this.currentFeedback.client_id
                };
                await sendPushToClient(push);
                await this.changeFeedbackStatus(e.body);
            },
            async changeFeedbackStatus(message = null) {
                await this.$store.dispatch(ACTIONS.CHANGE_FEEDBACK_STATUS,
                    {id: this.currentFeedback.id, answer: message, is_worked: true, user_id: this.$store.getters.user.id}
                );
                this.showMessageModal = false;
                showToast('Заявка обработана');
            },
            async onConfirm() {
                await this.changeFeedbackStatus();
                this.confirmModal = false;
                this.currentFeedback = null;
            }
        }
    }
</script>
