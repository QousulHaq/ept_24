<template>
  <div>
    <a-alert v-if="isBanned" type="error" :message="bannedText.message" :description="bannedText.description"/>
    <div v-else-if="state === 'loading'">
      <a-spin tip="Loading...">
        <div class="spin-content">
          downloading content. please wait
        </div>
      </a-spin>
      <a-progress :percent="itemLoadedPercentage" status="active" />
    </div>
    <a-empty v-else-if="state === 'unloaded'" :image-style="{ height: '60px' }">
      <span slot="description">Your exam properties does not loaded yet. click button below to reload.</span>
      <a-button type="primary" @click="boot">
        Reload
      </a-button>
    </a-empty>
    <router-view v-else-if="state === 'loaded'" />
  </div>
</template>


<script>
  import { mapGetters, mapActions } from 'vuex'

  export default {
    name: "PerformIndex",
    data: () => ({
      state: 'unloaded',
      bannedText: {
        message: 'You have been disqualified!',
        description: 'If you feels don\'t do anything wrong, ask proctor to let you continue your exam...'
      }
    }),
    computed: {
      ...mapGetters('exam', ['hasEnrolledExam', 'activeExam', 'isStarted', 'isBanned']),
      ...mapGetters('exam/perform', ['sections', 'itemLoadedPercentage']),
      isExamEnded: function () {
        return this.sections.every(s => s.ended_at !== null)
      }
    },
    watch: {
      isExamEnded: {
        immediate: true,
        handler: function (value) {
          if (value) {
            this.toGoodbyePage()
          }
        }
       },
      isBanned: {
        immediate: true,
        handler: function (value) {
          if (value) {
            this.$notification.error({
              message: this.bannedText.message,
              description: this.bannedText.description,
              placement: 'bottomLeft'
            })
          }
        }
      }
    },
    methods: {
      ...mapActions('exam/perform', {
        fetchSections: 'fetchSections'
      }),
      toGoodbyePage () {
        if (! this.$route.matched.some(r => r.name === 'perform.bye')) {
          this.$router.replace({ name: 'perform.bye' })
        }
      },
      toWaitingPage () {
        if (! this.$route.matched.some(r => r.name === 'perform.waiting')) {
          this.$router.replace({ name: 'perform.waiting' })
        }
      },
      toTacklePage () {
        if (! this.$route.matched.some(r => r.name === 'perform.tackle')) {
          this.$router.replace({ name: 'perform.tackle' })
        }
      },
      boot () {
        this.state = 'loading'
        this.fetchSections().then(() => {
          this.state = 'loaded'
          // determine started or not then redirect to proper page if currently is not
          if (this.isExamEnded) {
            this.toGoodbyePage()
          } else if (this.isStarted) {
            this.toTacklePage()
          } else {
            this.toWaitingPage()
          }
        }).catch(e => {
          console.error(e)
          this.state = 'unloaded'
        })
      }
    },
    mounted () {
      if (! this.hasEnrolledExam)
        return this.$router.push({ name: 'exam.list' })

      this.$nextTick(function () {
        if (! this.isBanned)
          this.boot()
      })
    }
  }
</script>

<style scoped>
  .spin-content {
    border: 1px solid #91d5ff;
    background-color: #e6f7ff;
    padding: 30px;
  }
</style>
