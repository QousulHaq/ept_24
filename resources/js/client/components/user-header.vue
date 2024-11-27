<template>
  <div class="user-wrapper">
    <div class="content-box">
      <a-popover
        class="action"
        v-model="visible"
        trigger="click"
        placement="bottomRight"
        overlayClassName="header-notice-wrapper"
        :getPopupContainer="() => $refs.noticeRef.parentElement"
        :autoAdjustOverflow="true"
        :arrowPointAtCenter="true"
        :overlayStyle="{ width: '300px', top: '50px'}">
        <template slot="content">
          <a-list>
            <a-list-item v-for="(item, key) in items.data" :key="`notification-${key}`">

            </a-list-item>
          </a-list>
        </template>
        <span @click="toggleVisibility" class="header-notice" ref="noticeRef" style="padding: 0 18px">
          <a-badge :count="items.total">
            <a-icon type="bell"/>
          </a-badge>
        </span>
      </a-popover>
      <a-dropdown>
        <span class="action ant-dropdown-link user-dropdown-menu">
          <span>{{ user.name }}</span>
        </span>
        <a-menu slot="overlay" class="user-dropdown-menu-wrapper">
          <a-menu-item key="auth.logout">
            <a @click="logout" href="#">
              <a-icon type="logout" />
              <span>Log Out</span>
            </a>
          </a-menu-item>
        </a-menu>
      </a-dropdown>
    </div>
  </div>
</template>
<script>
  import { mapState, mapActions } from 'vuex'

  export default {
    name: "UserHeader",
    computed: {
      notificationUri() {
        return '';
      },
      ...mapState('auth', {
        user: state => state.user
      })
    },
    data() {
      return {
        loading: false,
        visible: false,
        items: {}
      }
    },
    mounted() {

    },
    methods: {
      ...mapActions('auth', {
        logout: "logout"
      }),
      toggleVisibility() {
        this.visible = ! this.visible
      },
      getItems() {
        this.loading = true

      }
    }
  }
</script>
