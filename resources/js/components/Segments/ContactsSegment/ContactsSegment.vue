<template>
    <v-form>
        <v-text-field v-model="contacts.title" label="Название организации" :readonly="!isAdmin"/>
        <h3>Информация:</h3>
        <div class="d-flex align-items-center">
            <div style="flex-grow: 1">
                <div class="d-flex" v-for="(c, key) of contacts.information" :key="key">
                    <v-text-field
                        :readonly="!isAdmin"
                        label="Заголовок"
                        v-model="c.key"
                        style="margin-top: 5px; margin-right: 10px;"/>
                    <v-textarea
                        :readonly="!isAdmin"
                        auto-grow
                        rows="1"
                        style="margin-left: 10px;"
                        v-model="c.value"
                        label="Значение"/>
                    <v-btn icon @click="removeInformation(key)" v-if="isAdmin">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </div>
            </div>
            <v-btn icon @click="addInformation()" v-if="isAdmin">
                <v-icon>mdi-plus</v-icon>
            </v-btn>
        </div>
        <h3>Телефоны:</h3>
        <div class="d-flex align-items-center">
            <div style="flex-grow: 1">
                <div class="d-flex" v-for="(c, key) of contacts.phones" :key="key">
                    <v-text-field
                        :readonly="!isAdmin"
                        label="Заголовок"
                        v-model="c.key"
                        style="margin-top: 5px; margin-right: 10px;"/>
                    <v-text-field
                        :readonly="!isAdmin"
                        style="margin-left: 10px;"
                        v-model="c.value"
                        v-mask="'+7 ### ### ## ##'"
                        label="Значение"/>
                    <v-btn icon @click="removePhone(key)"  v-if="isAdmin">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </div>
            </div>
            <v-btn icon @click="addPhones()"  v-if="isAdmin">
                <v-icon>mdi-plus</v-icon>
            </v-btn>
        </div>
        <div class="d-flex">
            <iframe :src="contacts.map" height="300" width="500" frameborder="0"></iframe>
            <v-text-field v-model="contacts.map" label="Ссылка на карту" v-if="isAdmin"/>
        </div>
        <v-btn color="primary" block class="mt-5" @click="saveContacts" v-if="isAdmin">Сохранить</v-btn>
    </v-form>
</template>

<script>
    import {getContacts, updateContacts} from "../../../api/mobile";
    import showToast from "../../../utils/Toast";

    export default {
        data: () => ({
            contacts: {
                id: -1,
                title: '',
                information: [],
                phones: [],
                map: '',
            }
        }),
        props: {
            isAdmin: {
                type: Boolean
            }
        },
        async mounted() {
           await this.getContacts()
        },
        methods: {
            async getContacts() {
                const data = await getContacts();
                this.contacts.id = data.id;
                this.contacts.title = data.title;
                this.contacts.information = JSON.parse(data.information);
                this.contacts.phones = data.phones ? JSON.parse(data.phones) : [{key: '', value: ''}];
                this.contacts.map = data.map;
            },
            addInformation() {
                this.contacts.information.push({
                    key: '',
                    value: ''
                })
            },
            addPhones() {
                this.contacts.phones.push({
                    key: '',
                    value: '',
                })
            },
            async saveContacts() {
                const _contacts = {...this.contacts};
                _contacts.information = _contacts.information.filter(i => i.key && i.value);
                _contacts.phones = _contacts.phones.filter(i => i.key && i.value);
                _contacts.information = JSON.stringify(_contacts.information);
                _contacts.phones = JSON.stringify(_contacts.phones);

                await updateContacts(_contacts);

                showToast("Контакты были обновлены!");

            },

            removeInformation(i) {
                this.contacts.information.splice(i, 1);
            },
            removePhone(i) {
                this.contacts.phones.splice(i, 1);
            }
        }

    }
</script>

<style scoped>

</style>
