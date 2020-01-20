<template>
    <div>
        <v-card>
            <v-card-title>
                Поля клиента
            </v-card-title>
            <v-card-text>
                <div class="p-3">
                    <v-btn color="primary" @click="showModal = true">Добавить поле</v-btn>
                    <v-simple-table>
                        <template v-slot:default>
                            <thead>
                            <tr>
                                <th>Название поля</th>
                                <th>Статус</th>
                                <th>Действие</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(item) of fields" :key="item.id">
                                <td>{{ item.alias }}</td>
                                <td>
                                        <span :class="item.is_active == 1 ? 'green--text' : 'red--text'">
                                            {{  item.is_active == 1 ? 'Активно' : 'Неактивно' }}
                                        </span>
                                </td>
                                <td>
                                    <v-btn icon @click="changeStatus(item)">
                                        <v-icon v-if="item.is_active == 0">mdi-eye</v-icon>
                                        <v-icon v-if="item.is_active == 1">mdi-eye-off</v-icon>
                                    </v-btn>
                                    <v-btn icon @click="delete_id = item.id; deleteModal = true;">
                                        <v-icon>mdi-delete</v-icon>
                                    </v-btn>
                                </td>
                            </tr>
                            </tbody>
                        </template>
                    </v-simple-table>
                </div>
            </v-card-text>
        </v-card>
        <AddClientFieldModal :state="showModal" v-on:onClose="showModal = false"/>
        <ConfirmationModal
            :state="deleteModal"
            :message="'Вы действительно хотите удалить выбранное поле?'"
            v-on:cancel="deleteModal = false; delete_id = null"
            v-on:confirm="deleteItem"
        />
    </div>
</template>

<script>
    import AddClientFieldModal from "../../../components/Modals/AddClientFieldModal/AddClientFieldModal";
    import ConfirmationModal from "../../../components/Modals/ConfirmationModal/ConfirmationModal";

    export default {
        components: {ConfirmationModal, AddClientFieldModal},
        computed: {
            fields() {
                return this.$store.getters.get_fields;
            }
        },
        data: () => ({
            showModal: false,
            delete_id: null,
            deleteModal: false,
        }),
        methods: {
            async changeStatus(_item) {
                const item = {
                    id: _item.id,
                    is_active: _item.is_active == 1 ? 0 : 1
                };

                await this.$store.dispatch('changeField', item);

            },
            async deleteItem() {
                await this.$store.dispatch('deleteField', this.delete_id);
                this.delete_id = null;
                this.deleteModal = false;
            }
        }
    }
</script>

<style scoped>

</style>
