import React, { Component, useRef } from 'react';
import 
{ 
    StyleSheet, 
    View, 
    Text, 
    ScrollView, 
    AsyncStorage, 
    Alert, 
    ActivityIndicator, 
    Animated  
} from 'react-native';

class Cours extends Component {
    constructor(props){
        super(props);
        this.state = {
            cours: []
        }
    }
    
    componentDidMount(){
        AsyncStorage.getItem('idGroup').then(this.cours);
    }
    cours = (value) => {
        fetch('https://easypeps.be/coursUser/'+value)
        .then((response) => response.json())
        .then((responseJson) => {
            if(responseJson == "probleme"){
                Alert.alert(
                    "",
                    "Vous n'avez pas de cours veuillez en créer sur notre site web",
                    [
                        {
                            text: "OK", onPress: () => this.props.navigation.navigate("MenuGroup") 
                        }
                    ]
                );
            }
            else {
                this.setState({cours: responseJson})
            }
        })
    }
    render(){
        let coursUser = this.state.cours;  
        let afficheCours = [];
        if(coursUser.length === 0) {
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
        for(let i = 0; i < coursUser.length; i++){
            afficheCours.push(
                <View key= { i }>
                    <FadeInView>
                        <View key = { i } style={style.container}>
                            <Text style={{ fontSize: 20, color: 'white' }}>{coursUser[i]['cours']}</Text>
                            <Text style={{color: 'white'}}>Date de cours: {coursUser[i]['date_cours']}</Text>
                            <Text style={{color: 'white'}}>Nombre d'heures: {coursUser[i]['heures']}</Text>
                            <Text style={{color: 'white'}}>Periode: {coursUser[i]['periode']}</Text>
                        </View>
                    </FadeInView>
                </View>
            )
        }
        return (
            <ScrollView>
                { afficheCours }
            </ScrollView>
        )
    }
}
const style= StyleSheet.create({
    container: {
        flex : 1,
        backgroundColor: 'red',
        alignItems: 'center',
        justifyContent: 'space-between',
        marginTop: 10,
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
})
export default Cours