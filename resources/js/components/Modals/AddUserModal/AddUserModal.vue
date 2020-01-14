<template>
    <v-dialog persistent max-width="700" v-model="state">
        <v-card>
            <v-card-title class="d-flex justify-content-between blue darken-1">
                <span class="white--text">{{ title }}</span>
                <v-btn icon @click="$emit('onClose')" color="white">
                    <v-icon>mdi-close</v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-form v-if="!loading">
                    <v-text-field label="Имя" v-model="user.name"></v-text-field>
                    <v-text-field label="Логин" v-model="user.login"></v-text-field>
                    <v-checkbox color="primary" label="Изменить пароль" v-model="changePassword" v-if="editMode"/>
                    <v-text-field label="Пароль" v-model="user.password" v-if="changePassword || !editMode"></v-text-field>
                    <v-select
                        label="Роль"
                        :items="roles"
                        item-value="id"
                        item-text="name"
                        v-model="user.role_id"
                    />
                </v-form>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions v-if="!loading">
                <v-btn text @click="$emit('onClose')">Отмена</v-btn>
                <v-spacer></v-spacer>
                <v-btn text @click="onSave" color="success">
                    Сохранить
                    <v-icon>mdi-check</v-icon>
                </v-btn>
            </v-card-actions>
            <v-progress-linear
                indeterminate
                :active="loading"
                color="green"
                absolute
                bottom
            ></v-progress-linear>
        </v-card>
    </v-dialog>
</template>

<script>
    export default {
        watch: {
            state(oldValue, currentValue) {
                if (oldValue && this.editMode) {
                    const user = this.$store.getters.user_by_id(this.id);
                    this.user = {
                        ...user,
                    };
                    delete this.user.token;
                } else {
                    this.user = {
                        name: '',
                        login: '',
                        password: '',
                        role_id: null
                    };
                }
            }
        },
        methods: {
            async onSave() {
                this.loading = true;
                if (!this.editMode) {
                    const _user = {
                        name: this.user.name,
                        login: this.user.login,
                        role: this.user.role_id,
                        password: this.user.password
                    };

                    await this.$store.dispatch('createUser', _user);
                }

                else {
                    const user = {
                        id: this.user.id,
                        name: this.user.name,
                        login: this.user.login,
                        role: this.user.role_id,
                    };

                    if (this.changePassword) {
                        user.password = this.user.password;
                    } else {
                        delete user.password;
                    }

                    await this.$store.dispatch('editUser', user);
                }
                this.loading = false;
                this.changePassword = false;
                this.user = {name: '', };
                this.$emit('onSave')
            },
        },
        computed: {
            roles() {
                return this.$store.getters.get_roles;
            }
        },
        data: () => ({
            loading: false,
            changePassword: false,
            user: {
                name: '',
                login: '',
                password: '',
                role_id: null
            },
        }),
        props: {
            state: {
                type: Boolean,
                default: false,
            },
            title: {
                type: String,
                default: 'Добавление пользователя'
            },
            editMode: {
                type: Boolean,
                default: false
            },
            id: {
                type: Number,
                default: null
            }
        }
    }
</script>

<style scoped>

</style>
