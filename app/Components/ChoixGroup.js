import React, { Component, useRef } from 'react';
import 
{ 
    StyleSheet, 
    View, 
    TouchableOpacity, 
    Text, 
    ScrollView, 
    AsyncStorage, 
    Alert, 
    ActivityIndicator, 
    Animated 
} from 'react-native';

class ChoixGroup extends Component {
    constructor(props){
        super(props);
        this.state = {
            groups: [],
        }
    }
    
    componentDidMount(){
        AsyncStorage.getItem('idUser').then(this.getGroups)
    }

    getGroups = async (value) => {
        await fetch('http://192.168.1.3/groupsUser/'+value)
        .then((response) => response.json())
        .then((responseJson) => {
            if(responseJson == "probleme"){
                Alert.alert(
                    "",
                    "Vous n'avez pas de groupes disponible, veuillez en créer sur notre site web"
                );
            }
            else {
                this.setState({groups: responseJson})
            }
        
        })
        
    }

    render(){   
        let groups = this.state.groups;
        let ecoles = [];
        if(groups.length === 0) {
            return( 
                <View style={style.loading}>
                    <ActivityIndicator size="large" color="red" />
                </View>
            )
        } 
        const FadeInView = (props) => {
            const fadeAnim = useRef(new Animated.Value(0)).current  // Initial value for opacity: 0
          
            React.useEffect(() => {
              Animated.timing(
                fadeAnim,
                {
                  toValue: 1,
                  duration: 800,
                }
              ).start();
            }, [])
          
            return (
              <Animated.View                 // Special animatable View
                style={{
                  ...props.style,
                  opacity: fadeAnim,         // Bind opacity to animated value
                }}
              >
                {props.children}
              </Animated.View>
            );
          }
        for(let i = 0; i<groups.length; i++){
            ecoles.push(
                <View>
                    <FadeInView>
                        <TouchableOpacity style={style.button} onPress={() => this.props.navigation.navigate('MenuGroup', AsyncStorage.setItem('idGroup', groups[i]['groups_id']))}>
                            <Text style={{ fontSize: 30, color: 'white' }}>{groups[i]['groupes']}</Text>
                            <Text style={{ fontSize: 15, color: 'white' }}>{groups[i]['ecole']}</Text>
                        </TouchableOpacity>
                    </FadeInView>
                </View>
            )
        }
        return (       
            <ScrollView>
                { ecoles }
            </ScrollView>
        )
    }
}
const style= StyleSheet.create({
    button: {
        flex : 1,
        backgroundColor: 'red',
        alignItems: 'center',
        justifyContent: 'space-between',
        marginTop: 3,
        marginLeft: 10,
        width: '95%',
		borderRadius: 25,
		marginVertical: 10,
		paddingVertical: 13,
        textAlign: 'center',
        color: '#FFFFFF'
    },
    loading: {
        flex: 1,
        alignItems: "center",
        justifyContent: "center",
    }
});


export default ChoixGroup