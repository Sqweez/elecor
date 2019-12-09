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
                            <tr v-for="(item, index) of clientFields" :key="index">
                                <td>{{ item.name }}</td>
                                <td>
                                        <span :class="item.status === 1 ? 'green--text' : 'red--text'">
                                            {{  item.status === 1 ? 'Активно' : 'Неактивно' }}
                                        </span>
                                </td>
                                <td>
                                    <v-btn icon>
                                        <v-icon v-if="item.status === 0">mdi-eye</v-icon>
                                        <v-icon v-if="item.status === 1">mdi-eye-off</v-icon>
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
    </div>
</template>

<script>
    import AddClientFieldModal from "../../../components/Modals/AddClientFieldModal/AddClientFieldModal";

    export default {
        components: {AddClientFieldModal},
        data: () => ({
            showModal: false,
            clientFields: [
                {
                    name: 'ИИН/БИН',
                    status: 1
                },
                {
                    name: 'Доверенный телефон',
                    status: 0
                }
            ]
        }),
    }
</script>

<style scoped>

</style>
