var MenuComponent = {
  template: `
    <div class='container'>
{{ currentComponent() }}
      Team - {{ team.team_name }} - {{ game }}
      <b-row>
        <b-col cols='10'>
        </b-col>
        <b-col cols='2' class='text-center'>
          <b-button variant='link' size='lg' href='/jhmlhome.html'>
            JHML Home
          </b-button>
        </b-col>
      </b-row>
      <div v-show="! loading()">
        <h1><center>{{ year() }} {{ team.team_name }} {{ gameInProgress() ? 'during' : 'before' }} game {{ game }}</center></h1>
        <div class='alert alert-danger text-center' role='alert' v-show='! rosterValid()'>Your roster is invalid - You may not start a series</div>
        <b-row class='main-menu'>
          <b-col cols='12' class='text-center'>
            <b-button v-show='team.team_name != "guest"'
                      variant='primary' 
                      size='lg' 
                      v-on:click='switchToRoster();' 
                      v-bind:disabled='!rosterEnabled()'>
              Roster Management
            </b-button>
          </b-col>
        </b-row>
        <b-row class='main-menu'>
          <b-col cols='12' class='text-center'>
            <b-button v-show='team.team_name != "guest"'
                      variant='primary' 
                      size='lg' 
                      v-on:click='switchToSchedule();'
                      v-bind:disabled='! scheduleEnabled()'>
              Schedule
            </b-button>
          </b-col>
        </b-row>
        <b-row class='main-menu' v-show="nextSeries()">
          <b-col cols='12' class='text-center'>
            <b-button variant='primary' 
                      size='lg' 
                      v-on:click='switchToSchedule();'>
              Next Series
            </b-button>
          </b-col>
        </b-row>
        <b-row class='main-menu' v-show="nextGame()">
          <b-col cols='12' class='text-center'>
            <b-button variant='primary' 
                      size='lg' 
                      v-on:click='switchToSchedule();'>
              Next Game
            </b-button>
          </b-col>
        </b-row>
        <b-row class='main-menu' v-show="resumeGame()">
          <b-col cols='12' class='text-center'>
            <b-button variant='primary' 
                      size='lg' 
                      v-on:click='switchToSchedule();'>
              Resume Game
            </b-button>
          </b-col>
        </b-row>
      </div>
      <div v-show="loading()" class="footer">
        <b-row class='pb-5'>
          <b-col cols="1">
          </b-col>
          <b-col cols="11">
            <h3>
              Loading
              <span v-show="loadingConfig() && ! loadingRoster() && ! loadingRotation() && ! loadingSchedule()">Config</span>
              <span v-show="loadingConfig() && (loadingRoster() || loadingRotation() ||  loadingSchedule())">Config,</span>
              <span v-show="loadingRoster() && ! loadingRotation() && ! loadingSchedule()">Roster</span>
              <span v-show="loadingRoster() && (loadingRotation() || loadingSchedule())">Roster,</span>
              <span v-show="loadingRotation() && ! loadingSchedule()">Rotation</span>
              <span v-show="loadingRotation() && loadingSchedule()">Rotation,</span>
              <span v-show="loadingSchedule()">Schedule</span>
            </h3>
          </b-col>
        </b-row>
      </div>
    </div>
  `,
  data: function() {
    return {
      game: 1,
      team: {"team_name":""},
    }
  },
  mounted() {
    eBus.$on('teamUpdated',(t) => { this.team = t;});
    eBus.$on('rosterUpdated',(r) => { });
    eBus.$on('scheduleUpdated',(s) => { });
    eBus.$on('gameUpdated',(g) => { this.game = g;});
  },
  methods: {
    currentComponent() { if (vue == undefined) return ''; return vue.currentComponent; },
    rosterValid() { if (vue == undefined) return false; return vue.rosterValid(); },
    loading() { if (vue == undefined) return true; return vue.loading(); },
    loadingConfig() { if (vue == undefined) return true; return vue.loadingConfig; },
    loadingRoster() { if (vue == undefined) return true; return vue.loadingRoster; },
    loadingRotation() { if (vue == undefined) return true; return vue.loadingRotation; },
    loadingSchedule() { if (vue == undefined) return true; return vue.loadingSchedule; },
    gameInProgress() { if (vue == undefined) return false; return vue.gameInProgress; },
    year() { if (vue == undefined) return ''; return vue.year; },
    switchToRoster() {
      vue.currentComponent='rosterComponent';
      //vue.emitData();
    },
    switchToSchedule() {
      vue.currentComponent='scheduleComponent';
      //vue.emitData();
    },
    rosterEnabled() {
      if (vue === undefined) return false;
      if (JSON.stringify(vue.roster) == '{}') return false;
      if (vue.gameInProgress === true) return false;
      if (vue.betweenSeries === false && vue.injury === false) return false;
      return true;
    },
    scheduleEnabled() {
      if (vue === undefined) return false;
      return JSON.stringify(vue.schedule) != '{}';
    },
    nextSeries() {
      if (vue === undefined) return false;
      if (JSON.stringify(vue.schedule) == '{}') return false;
      if (! this.scheduleEnabled()) return false;
      if (! vue.rosterValid()) return false;
      if (vue.gameInProgress === true) return false;
      if (vue.betweenSeries === false) return false;
      //console.log(vue.schedule);
      return true;
    },
    nextGame() {
      if (vue === undefined) return false;
      if (JSON.stringify(vue.schedule) == '{}') return false;
      if (! this.scheduleEnabled()) return false;
      if (! vue.rosterValid()) return false;
      if (vue.gameInProgress === true) return false;
      if (vue.betweenSeries === true) return false;
      return true;
    },
    resumeGame() {
      if (vue === undefined) return false;
      if (JSON.stringify(vue.schedule) == '{}') return false;
      if (! this.scheduleEnabled()) return false;
      if (! vue.rosterValid()) return false;
      if (vue.gameInProgress === false) return false;
      return true;
    },
  },
};

