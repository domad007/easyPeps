import React, { Component } from 'react';
import { StyleSheet, View, TouchableOpacity, Text, ScrollView, flexDirection, AsyncStorage } from 'react-native';

export default class MenuGroup extends Component {
    constructor(props){
        super(props);
        this.state = {
            idGroup: ""
        }
    }
    loadGroup = async () => {
        let groupId = await AsyncStorage.getItem('idGroup');
        this.setState({idGroup: groupId})
    }

    componentDidMount(){
        this.loadGroup().done()
    }
    render(){
        return (
            <ScrollView>
                <View>
                    <TouchableOpacity 
                        style={style.button} 
                        onPress={ () => this.props.navigation.navigate('CahierCotes') }
                    >
                        <Text style={{fontSize: 20}}>Cahier de cotes</Text>
                    </TouchableOpacity>
                    <TouchableOpacity 
                        style={style.button}
                        onPress={ () => this.props.navigation.navigate('Cours') }
                    >
                        <Text style={{fontSize: 20}}>Cours</Text>
                    </TouchableOpacity>
                    <TouchableOpacity 
                        style={style.button}
                        onPress={ () => this.props.navigation.navigate('Evaluations') }
                    >
                        <Text style={{fontSize: 20}}>Evaluations</Text>
                    </TouchableOpacity>
                    <TouchableOpacity 
                        style={style.button}
                        onPress={ () => this.props.navigation.navigate('Eleves') }
                    >
                        <Text style={{fontSize: 20}}>Eleves</Text>
                    </TouchableOpacity>
                </View>
            </ScrollView>
        )
    }
}
const style= StyleSheet.create({
    button: {
        flex : 1,
        backgroundColor: '#fff',
        alignItems: 'center',
        justifyContent: 'space-between',
        marginTop: 10,
        marginLeft: 10,
        width: '95%',
        backgroundColor:'lightgrey',
		borderRadius: 25,
		marginVertical: 10,
		paddingVertical: 13,
        textAlign: 'center',
        color: '#FFFFFF'
    }
});