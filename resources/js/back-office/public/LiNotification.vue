<template>
  <li class="dropdown dropdown-list-toggle">
    <a href="#" data-toggle="dropdown" :class="iconClasses" aria-expanded="false">
      <i class="far fa-bell tw-text-black"></i>
    </a>
    <div class="dropdown-menu dropdown-list dropdown-menu-right">
      <div class="dropdown-header">Notifications
        <div class="float-right">
          <a href="#" v-show="unread && notifications.length > 0" @click.prevent="markAllAsRead">Mark All As Read</a>
        </div>
      </div>
      <div class="dropdown-list-content dropdown-list-icons" style="overflow: hidden; outline: currentcolor none medium;" tabindex="3">
        <a href="#" class="dropdown-item" v-for="notification in notifications" @click.prevent="detailNotification(notification)">
          <div class="dropdown-item-icon bg-success text-white" v-if="notification.data.level === 'success'">
            <i class="fas fa-check"></i>
          </div>
          <div class="dropdown-item-icon bg-danger text-white" v-else-if="notification.data.level === 'danger'">
            <i class="fas fa-exclamation-triangle"></i>
          </div>
          <div class="dropdown-item-icon bg-info text-white" v-else>
            <i class="fas fa-bell"></i>
          </div>
          <div class="dropdown-item-desc">
            {{ notification.data.message }}
            <div class="time">{{ dateFormat(notification.created_at) }}</div>
          </div>
        </a>
      </div>
      <div class="dropdown-footer text-center">
        <a href="#" @click.prevent="changeMode">{{ unread ? 'View All' : 'View Unread' }} <i class="fas fa-chevron-right"></i></a>
      </div>
      <div id="ascrail2002"
           class="nicescroll-rails nicescroll-rails-vr"
           style="width: 9px; z-index: 1000; cursor: default; position: absolute; top: 58px; left: 341px; height: 350px; opacity: 0.3; display: block;">
        <div style="position: relative; top: 0px; float: right; width: 7px; height: 306px; background-color: rgb(66, 66, 66); border: 1px solid rgb(255, 255, 255); background-clip: padding-box; border-radius: 5px;" class="nicescroll-cursors"></div></div>
      <div id="ascrail2002-hr" class="nicescroll-rails nicescroll-rails-hr" style="height: 9px; z-index: 1000; top: 399px; left: 0px; position: absolute; cursor: default; display: none; width: 341px; opacity: 0.3;">
        <div style="position: absolute; top: 0px; height: 7px; width: 350px; background-color: rgb(66, 66, 66); border: 1px solid rgb(255, 255, 255); background-clip: padding-box; border-radius: 5px; left: 0px;" class="nicescroll-cursors">
        </div>
      </div>
    </div>
  </li>
</template>

<script>
  import { formatRelative } from 'date-fns'

  export default {
    name: "LiNotification",
    data: () => ({
      unread: 1,
      matter: {}
    }),
    computed: {
      iconClasses: function () {
        return [
          'nav-link',
          'notification-toggle',
          'nav-link-lg',
          ...(this.unread && this.matter.hasOwnProperty('data') && this.matter.data.length > 0)
            ? ['beep']
            : []
        ]
      },
      notifications: function () {
        return this.matter.hasOwnProperty('data') ? this.matter.data : []
      }
    },
    methods: {
      getNotification () {
        this.request('api.notification', {}, {
          params: {
            unread: Number(this.unread)
          }
        }, false).then(({ data : responseData }) => {
          this.matter = responseData
        })
      },
      listenNotification () {
        this.request('api.user', {}, {}, false).then(({ data: responseData }) => {
          Echo.private('notification.' + responseData.username).notification(() => {
            this.getNotification()
          })
        })
      },
      markAllAsRead () {
        this.request('api.notification.read-all').then(this.getNotification)
      },
      detailNotification (notification) {
        this.swal(notification.data.message, notification.data.detail)
      },
      changeMode () {
        this.unread = ! this.unread
        this.getNotification()
      },
      dateFormat(time) {
        return formatRelative(new Date(time), new Date())
      }
    },
    mounted () {
      this.getNotification()
      this.listenNotification()
    }
  }
</script>

<style scoped>

</style>
