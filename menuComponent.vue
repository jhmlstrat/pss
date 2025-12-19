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
        <h1><center>{{ year() }} {{ team.team_name }} {{ gameInProgress() ? 'during' : 'before' }} game {{ game }} (day {{day }})</center></h1>
        <div class='alert alert-danger text-center' role='alert' v-show='! rosterValid()'>Your roster is invalid - You may not start a series</div>
        <b-row class='main-menu'>
          <b-col cols='12' class='text-center'>
            <b-button v-show='team.team_name != "guest"'
                      variant='primary' 
                      size='lg'
                      v-bind:title='rosterTooltip()'
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
      day: 1,
      team: {"team_name":""},
    }
  },
  mounted() {
    eBus.$on('teamUpdated',(t) => { this.team = t;});
    eBus.$on('rosterUpdated',(r) => { });
    eBus.$on('scheduleUpdated',(s) => { });
    eBus.$on('gameUpdated',(g) => { this.game = g; this.day = vue.day;});
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
    rosterTooltip() {
      if (vue === undefined) return '';
      rtn = 'vue.gameInProgress=' + (vue.gameInProgress ? 'true' : 'false') + '\n';
      rtn += 'vue.betweenSeries=' + (vue.betweenSeries ? 'true' : 'false') + '\n';
      rtn += 'vue.injury=' + (vue.injury ? 'true' : 'false') + '\n';
      return rtn;
    },
    rosterEnabled() {
console.log("r1");
      if (vue === undefined) return false;
console.log("r2");
console.log(vue.roster);
      if (JSON.stringify(vue.roster) == '{}') return false;
console.log("r3");
      if (vue.gameInProgress === true) return false;
console.log("r4");
      if (vue.betweenSeries === false && vue.injury === false) return false;
console.log("r5");
      return true;
    },
    scheduleEnabled() {
      if (vue === undefined) return false;
      return JSON.stringify(vue.schedule) != '{}';
    },
    nextSeries() {
console.log("ns1");
      if (vue === undefined) return false;
console.log("ns2");
      if (JSON.stringify(vue.schedule) == '{}') return false;
console.log("ns3");
      if (! this.scheduleEnabled()) return false;
console.log("ns4");
      if (! vue.rosterValid()) return false;
console.log("ns5");
      if (vue.gameInProgress === true) return false;
console.log("ns6");
      if (vue.betweenSeries === false) return false;
console.log("ns7");
      //console.log(vue.schedule);
      return true;
    },
    nextGame() {
console.log("ng1");
      if (vue === undefined) return false;
console.log("ng2");
      if (JSON.stringify(vue.schedule) == '{}') return false;
console.log("ng3");
      if (! this.scheduleEnabled()) return false;
console.log("ng4");
      if (! vue.rosterValid()) return false;
console.log("ng5");
      if (vue.gameInProgress === true) return false;
console.log("ng6");
      if (vue.betweenSeries === true) return false;
console.log("ng7");
      return true;
    },
    resumeGame() {
console.log("rg1");
      if (vue === undefined) return false;
console.log("rg2");
      if (JSON.stringify(vue.schedule) == '{}') return false;
console.log("rg3");
      if (! this.scheduleEnabled()) return false;
console.log("rg4");
      if (! vue.rosterValid()) return false;
console.log("rg5");
      if (vue.gameInProgress === false) return false;
console.log("rg6");
      return true;
    },
  },
};

