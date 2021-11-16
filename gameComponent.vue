var GameComponent = {
  name: 'GameComponent',
  template: `
    <div class='container'>
{{ currentComponent }}
      <b-row>
        <b-col cols='2' class='text-center'>
          <b-button variant='link' size='lg' href='#' v-on:click='switchToMenu();'>
            Pss Menu
          </b-button>
        </b-col>
        <b-col cols='8'>
        </b-col>
        <b-col cols='2' class='text-center'>
          <b-button variant='link' size='lg' href='/jhmlhome.html'>
            JHML Home
          </b-button>
        </b-col>
      </b-row>
      <hr/>
      <b-row no-gutters>
        <b-col cols='6'>
          <situation-component v-bind:gameInfo="gameInfo"
                             v-bind:vRoster="team.team == gameInfo.visitor.name ? roster : oroster"
                             v-bind:hRoster="team.team == gameInfo.visitor.name ? oRoster : roster">
          </situation-component>
        </b-col>
        <b-col cols='6'>
          <line-score-component v-bind:gameInfo="gameInfo"></line-score-component>
        </b-col>
      </b-row>
      <hr/>
      <b-row>
        <b-col cols='12'>
          <game-entry-component v-bind:gameInfo="gameInfo"></game-entry-component>
        </b-col>
      </b-row>
      <b-row class='mt-5'>
        <b-col cols='12'>
          <defense-component v-bind:gameInfo="gameInfo" 
                             v-bind:vRoster="team.team == gameInfo.visitor.name ? roster : oroster"
                             v-bind:hRoster="team.team == gameInfo.visitor.name ? oRoster : roster">
          </defense-component>
        </b-col>
      </b-row>
    </div>
  `,
  data() {
    return {
    }
  },
  components: {
    'game-entry-component': GameEntryComponent,
    'line-score-component': LineScoreComponent,
    'situation-component': SituationComponent,
    'defense-component': DefenseComponent,
  },
  watch: {
  },
  mounted() {
  },
  computed: {
    gameInfo() { if (vue == undefined) return {'visitor':{'name':'','gameNumber':''},'home':{'name':'','gameNumber':''}}; return vue.gameInfo; },
    currentComponent() { if (vue == undefined) return ''; return vue.currentComponent; },
    team() { if (vue == undefined) return {'team':'None'}; return vue.team; },
    roster() { if (vue == undefined) return {}; return vue.roster; },
    oRoster() { if (vue == undefined) return {}; return vue.oRoster; },
  },
  methods: {
    switchToMenu() {
      vue.currentComponent='menuComponent';
      vue.emitData();
    },
  },
};

