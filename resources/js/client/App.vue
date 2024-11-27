<template>
  <a-layout id="app" class="layout base-layout">
    <a-layout-header class="header ant-header-fixedHeader dark" >
      <user-header />
    </a-layout-header>

    <div class="page-container">
      <div class="page-content">
        <transition name="fade">
          <router-view/>
        </transition>
      </div>
    </div>
  </a-layout>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import UserHeader from './components/user-header'

  export default {
    name: "RootApp",
    components: { UserHeader },
    computed: {
      ...mapGetters('auth', ['authenticated']),
      ...mapState('auth', {
        connectionState: state => state.connection_state,
        isConnected: state => state.connection_state === 'connected'
      })
    },
    watch: {
      connectionState: function (value) {
        this.$notification[this.isConnected ? 'success' : 'warning']({
          message: value,
          description: {
            connecting: 'trying to reach server !',
            connected: 'connection established !',
            unavailable: 'connection timeout, check your internet connection !',
            failed: 'either your browser doesn\'t support this application, or connection problems !'
          }[value],
          placement: 'bottomLeft'
        })
      }
    }
  }
</script>

<style>
.editor__content,.ant-radio-button-wrapper {
  user-select: none;
}
</style>
