import React, { Component } from 'react'
import { createStackNavigator } from '@react-navigation/stack';
import Connexion from '../Components/Connexion'
import ChoixGroup from '../Components/ChoixGroup'
import MenuGroup from '../Components/MenuGroup'
import CahierCotes from '../Components/CahierCotes'
import Cours from '../Components/Cours'
import Evaluations from '../Components/Evaluations'
import Eleves from '../Components/Eleves'

const AppStack = createStackNavigator();
const ConnexionStack = createStackNavigator();
const GroupStack = createStackNavigator();
const ChoixGroupStack = createStackNavigator();
const CahierCotesStack = createStackNavigator();
const CoursStack = createStackNavigator();
const EvaluationsStack = createStackNavigator();
const ElevesStack = createStackNavigator();

function connexionAffichage(){
    return(
        <ConnexionStack.Navigator
            screenOptions={{
                headerShown: false
            }}
        >
            <ConnexionStack.Screen name="Connexion" component={Connexion} />
        </ConnexionStack.Navigator>
    )
}
function choixGroupAffichage(){
    return (
        <ChoixGroupStack.Navigator
            screenOptions={{
                headerShown: false
            }}
        >
            <ChoixGroupStack.Screen name="Choisissez votre groupe" component={ChoixGroup} />
        </ChoixGroupStack.Navigator>
    )
}

function menuGroupAffichage(){
    return (
        <GroupStack.Navigator
            screenOptions={{
                headerShown: false
            }}
        >
            <GroupStack.Screen name="MenuGroup" component={MenuGroup} />
        </GroupStack.Navigator>
    )
}

function cahierCotesAffichage(){
    return (
        <CahierCotesStack.Navigator
            screenOptions={{
                headerShown: false
            }}
        >
            <CahierCotesStack.Screen name="CahierCotes" component={CahierCotes} />   
        </CahierCotesStack.Navigator>
    )
}

function coursAffichage(){
    return (
        <CoursStack.Navigator
            screenOptions={{
                headerShown: false
            }}
        >
            <CoursStack.Screen name="Cours" component={Cours} /> 
        </CoursStack.Navigator>
    )
}

function evaluationsAffichage(){
    return (
        <EvaluationsStack.Navigator
            screenOptions={{
                headerShown: false
            }}
        >
            <EvaluationsStack.Screen name="Evaluations" component={Evaluations} />
        </EvaluationsStack.Navigator>
    )
}

function elevesAffichage(){
    return (
        <ElevesStack.Navigator
                screenOptions={{
                    headerShown: false
                }}
            >
                <ElevesStack.Screen name="Eleves" component={Eleves} />    
        </ElevesStack.Navigator>
    )
}

function appNavigation(){
    return (
        <AppStack.Navigator
            initialRouteName="Connexion"
        >    
            <AppStack.Screen 
                name="Connexion"
                component={connexionAffichage}
                options={{ title: 'Connexion', headerTintColor: 'white', headerStyle: { backgroundColor: 'red' }} }
            />  
            <AppStack.Screen 
                name="ChoixGroup"
                component={choixGroupAffichage}
                options={{ title: "Choisissez votre groupe", headerTintColor: 'white', headerStyle: { backgroundColor: 'red' } }}
            />
            <AppStack.Screen 
                name="MenuGroup"
                component={menuGroupAffichage}
                options={{ title: 'Menu du groupe', headerTintColor: 'white', headerStyle: { backgroundColor: 'red' }} }
            />
            <AppStack.Screen 
                name="CahierCotes"
                component={cahierCotesAffichage}
                options={{ title: 'Cahier de cotes', headerTintColor: 'white', headerStyle: { backgroundColor: 'red' }} }
            />
            <AppStack.Screen 
                name="Cours"
                component={coursAffichage}
                options={{ title: 'Cours', headerTintColor: 'white', headerStyle: { backgroundColor: 'red' }} }
            />
            <AppStack.Screen 
                name="Evaluations"
                component={evaluationsAffichage}
                options={{ title: 'Evaluations', headerTintColor: 'white', headerStyle: { backgroundColor: 'red' }} }
            />
            <AppStack.Screen 
                name="Eleves"
                component={elevesAffichage}
                options={{ title: 'Eleves', headerTintColor: 'white', headerStyle: { backgroundColor: 'red' }} }
            />
        </AppStack.Navigator>
    )
}
export default appNavigation;


