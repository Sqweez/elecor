<template>
    <nav>
        <div class="container">
            <div class="nav-top d-flex justify-content-between align-items-center">
                <router-link tag="div" to="/" class="logo d-flex flex-row justify-content-center align-items-center">
                    <div class="mr-5">
                        <a href="#">
                            <img src="../../../images/logo.png" alt="">
                        </a>
                    </div>
                </router-link>
                <div class="menu-wrapper d-flex">
                    <div class="badge">
                        <v-badge
                            overlap
                            small
                            color="red"
                            value="35"
                        >
                            <template v-slot:badge v-if="notificationsCount">
                                {{ notificationsCount }}
                            </template>
                            <v-btn icon small @click="showNotificationDropDown = !showNotificationDropDown">
                                <v-icon
                                    color="grey darken-1"
                                    @click="ordersNavigate"
                                >
                                    mdi-bell-outline
                                </v-icon>
                            </v-btn>
                        </v-badge>
                    </div>
                    <div class="user-dropdown" v-ripple>
                        <div class="user-dropdown-toggle d-flex align-items-center"
                             @click="showUserDropDown = !showUserDropDown"
                               >
                            <div class="user-info">
                                <div class="user-name">Катеринин Александр</div>
                                <div class="user-role">Администратор</div>
                            </div>
                            <v-avatar size="40" color="primary">
                                <img src="../../../images/logo.png" alt="">
                            </v-avatar>
                        </div>
                        <div class="user-dropdown-content" v-if="showUserDropDown">
                            <ul class="dropdown-list">
                                <li class="dropdown-item" v-ripple @click="settingsNavigate">
                                    <v-icon>mdi-settings-outline</v-icon>
                                    <span>Настройки</span>
                                </li>
                                <li class="dropdown-item" v-ripple @click="$router.push('/login')">
                                    <v-icon>mdi-logout</v-icon>
                                    <span>Выход</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="nav-control">
            <div class="container">
                <ul class="navigation d-flex align-items-center">
                    <NavItem v-for="(menuItems, index) of menuItems" :menu="menuItems" :key="index"/>
                </ul>
            </div>
        </div>
    </nav>
</template>

<script>
    import {mapGetters} from 'vuex';
    import NavItem from '../NavItem/NavItem';
    import showToast from '@/utils/Toast';
    import {getCount} from "../../../api/feedback";

    export default {
        data() {
            return {
                showUserDropDown: false,
                showNotificationDropDown: false,
                notificationsCount: 0,
            }
        },
        components: {
            NavItem,
        },
        computed: {
            ...mapGetters(['menuItems']),
        },
        async created() {
            this.notificationsCount = await getCount();
        },
        async mounted() {
          setInterval(async () => {
              this.notificationsCount = await getCount();
          }, 10000);
        },
        methods: {
            ordersNavigate() {
                if (this.notificationsCount > 0) {
                    try {
                        this.$router.push({name: 'Orders'});
                    } catch (e) {
                        //
                    }
                    return 0;
                }

                showToast('', 'Нет непрочитанных уведомлений!', 'warning');
            },
            settingsNavigate() {
                this.showUserDropDown = false;
                showToast('', 'Пункт еще не реализован!', 'warning');
            }
        }
    }
</script>

<style scoped lang="scss">
    @import "Navbar";
</style>
