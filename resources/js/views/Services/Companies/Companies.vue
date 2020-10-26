<template>
    <div>
        <v-card>
            <v-card-title>
                Компании
            </v-card-title>
            <v-card-text>
                <div class="p-3">
                    <div class="d-flex justify-content-end mb-3">
                        <v-btn color="primary" @click="addModal = true">
                            Добавить компанию
                            <v-icon>mdi-plus</v-icon>
                        </v-btn>
                    </div>
                    <v-simple-table>
                        <template v-slot:default>
                            <thead>
                            <tr>
                                <th>Название компании</th>
                                <th>Действие</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="company of companies" :key="company.id">
                                <td>
                                    <span v-if="edit_id !== company.id"> {{ company.name }}</span>
                                    <v-text-field v-if="editMode && edit_id === company.id" v-model="name" />
                                </td>
                                <td>
                                    <v-btn icon @click="delete_id = company.id; deleteModal = true;" v-if="!editMode">
                                        <v-icon>mdi-delete</v-icon>
                                    </v-btn>
                                    <v-btn icon @click="edit_id = company.id; name = company.name; editMode = true" v-if="!editMode">
                                        <v-icon>mdi-pencil</v-icon>
                                    </v-btn>
                                    <v-btn icon v-if="editMode" @click="edit_id = null; name = ''; editMode = false;">
                                        <v-icon>mdi-cancel</v-icon>
                                    </v-btn>
                                    <v-btn icon v-if="editMode" @click="onEdit">
                                        <v-icon>mdi-check</v-icon>
                                    </v-btn>
                                </td>
                            </tr>
                            </tbody>
                        </template>
                    </v-simple-table>
                </div>
            </v-card-text>
        </v-card>
        <AddCompanyModal
            :state="addModal"
            v-on:onClose="addModal = false"
        />
        <ConfirmationModal
            :state="deleteModal"
            :message="'Вы действительно хотите удалить выбранное поле?'"
            v-on:cancel="deleteModal = false; delete_id = null"
            v-on:confirm="deleteItem"
        />
    </div>
</template>

<script>
    import AddCompanyModal from "../../../components/Modals/AddCompanyModal/AddCompanyModal";
    import ConfirmationModal from "../../../components/Modals/ConfirmationModal/ConfirmationModal";
    import axios from 'axios';
    import showToast from "../../../utils/Toast";

    export default {
        components: {AddCompanyModal, ConfirmationModal},
        data: () => ({
            addModal: false,
            delete_id: null,
            deleteModal: false,
            edit_id: null,
            name: '',
            editMode: false,
        }),
        methods: {
            async deleteItem() {
                await this.$store.dispatch('DELETE_COMPANY', this.delete_id);
                this.delete_id = null;
                showToast('Компания удалена!');
                this.deleteModal = false;
            },
            async onEdit() {
                await this.$store.dispatch('EDIT_COMPANY', {
                    name: this.name,
                    id: this.edit_id
                })
                this.editMode = false;
                this.name = "";
                this.edit_id = null;
                showToast('Компания отредактирована!');

            }
        },
        computed: {
            companies() {
                return this.$store.getters.COMPANIES;
            }
        }
    }
</script>

<style scoped>

</style>
