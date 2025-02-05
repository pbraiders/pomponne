<?php
/*************************************************************************
 *                                                                       *
 * Copyright (C) 2010   Olivier JULLIEN - PBRAIDERS.COM                  *
 * Tous droits réservés - All rights reserved                            *
 *                                                                       *
 *************************************************************************
 *                                                                       *
 * Except if expressly provided in a dedicated License Agreement,you     *
 * are not authorized to:                                                *
 *                                                                       *
 * 1. Use,copy,modify or transfer this software component,module or      *
 * product,including any accompanying electronic or paper documentation  *
 * (together,the "Software").                                            *
 *                                                                       *
 * 2. Remove any product identification,copyright,proprietary notices    *
 * or labels from the Software.                                          *
 *                                                                       *
 * 3. Modify,reverse engineer,decompile,disassemble or otherwise         *
 * attempt to reconstruct or discover the source code,or any parts of    *
 * it,from the binaries of the Software.                                 *
 *                                                                       *
 * 4. Create derivative works based on the Software (e.g. incorporating  *
 * the Software in another software or commercial product or service     *
 * without a proper license).                                            *
 *                                                                       *
 * By installing or using the "Software",you confirm your acceptance     *
 * of the hereabove terms and conditions.                                *
 *                                                                       *
 * file encoding: UTF-8                                                  *
 *                                                                       *
 *************************************************************************/
if( !defined('PBR_VERSION') || !defined('PBR_DB_LOADED') || !defined('PBR_AUTH_LOADED') )
    die('-1');

/**
  * function: RentUpdate
  * description: Update a rent.
  * parameters: STRING|sLogin   - login identifier
  *             STRING|sSession - session identifier
  *             STRING|sInet    - concatenation of IP and USER_AGENT
  *              CRent|pRent    - instance of CRent with valid datas.
  * return: BOOLEAN - FALSE if an exception occures
  *         or
  *         INTEGER - >=0 is OK. Number of row updated.
  *                    -1 when a private error occures.
  *                    -2 when an authentication error occures.
  *                    -3 when an access denied error occures.
  *                    -4 when a duplicate error occures.
  * author: Olivier JULLIEN - 2010-02-04
  * update: Olivier JULLIEN - 2010-05-24 - use ErrorDBLog instead of CErrorList::AddDB(...) and CDBLayer::GetInstance()->LogError(...)
  * update: Olivier JULLIEN - 2010-06-15 - improvement
  */
function RentUpdate( $sLogin, $sSession, $sInet, CRent $pRent)
{
    /** Initialize
     *************/
    $iReturn = -1;
    $sMessage = '';
    $sErrorTitle = __FUNCTION__ .'('.$sLogin.','.$sSession.',[obfuscated],'.$pRent->GetIdentifier().',...)';

    /** Request
     **********/
    if( (CDBLayer::GetInstance()->IsOpen()===TRUE)
     && IsScalarNotEmpty(PBR_DB_DBN)
     && IsStringNotEmpty($sLogin)
     && IsStringNotEmpty($sSession)
     && IsStringNotEmpty($sInet)
     && ($pRent->GetIdentifier()>0) )
    {
        /** Start transaction
         ********************/
        CDBLayer::GetInstance()->BeginTransaction($sLogin);

        try
        {
             /** Update max for all the reservation of the day
             ************************************************/

            // Prepare
            $sSQL = 'UPDATE `'.PBR_DB_DBN.'`.`reservation` AS r INNER JOIN `'.PBR_DB_DBN.'`.`reservation` AS s USING(`year`,`month`,`day`) SET r.`rent_max`=:iMax WHERE s.`idreservation`=:iIdentifier';
            $pPDOStatement = CDBLayer::GetInstance()->GetDriver()->prepare($sSQL);
            // Bind
            $pPDOStatement->bindValue(':iMax',$pRent->GetMax(),PDO::PARAM_INT);
            $pPDOStatement->bindValue(':iIdentifier',$pRent->GetIdentifier(),PDO::PARAM_INT);
            // Execute
            $pPDOStatement->execute();
            // Count
            $iReturn = $pPDOStatement->rowCount();
            // Free resource
            $pPDOStatement = NULL;

             /** Update rent
             *************/

            // Prepare
            $sSQL = 'UPDATE `'.PBR_DB_DBN.'`.`reservation` SET `rent_real`=:iReal, `rent_planned`=:iPlanned, `rent_canceled`=:iCanceled, `age`=:iAge, `horaire`=:iHoraire, `arrhe`=:iArrhes, `comment`=:sComment, `update_date`=SYSDATE(), `update_iduser`=:iUserId WHERE `idreservation`=:iIdentifier';
            $pPDOStatement = CDBLayer::GetInstance()->GetDriver()->prepare($sSQL);
            // Bind
            $pPDOStatement->bindValue(':iIdentifier',$pRent->GetIdentifier(),PDO::PARAM_INT);
            $pPDOStatement->bindValue(':iReal',$pRent->GetCountReal(),PDO::PARAM_INT);
            $pPDOStatement->bindValue(':iPlanned',$pRent->GetCountPlanned(),PDO::PARAM_INT);
            $pPDOStatement->bindValue(':iCanceled',$pRent->GetCountCanceled(),PDO::PARAM_INT);
            $pPDOStatement->bindValue(':iAge',$pRent->GetAge(),PDO::PARAM_INT);
            $pPDOStatement->bindValue(':iHoraire',$pRent->GetHoraire(),PDO::PARAM_INT);
            $pPDOStatement->bindValue(':iArrhes',$pRent->GetArrhes(),PDO::PARAM_INT);
            $pPDOStatement->bindValue(':sComment',$pRent->GetComment(),PDO::PARAM_STR);
            $pPDOStatement->bindValue(':iUserId',CAuth::GetInstance()->GetUserBDIdentifier(),PDO::PARAM_INT);
            // Execute
            $pPDOStatement->execute();
            // Count
            $iReturn = $iReturn + $pPDOStatement->rowCount();

            /** Commit transaction
             *********************/
            CDBLayer::GetInstance()->Commit($sLogin);

        }
        catch(PDOException $e)
        {
            $iReturn = FALSE;
            $sMessage = $e->getMessage();
            CDBLayer::GetInstance()->RollBack($sLogin);
        }//try

        // Free resource
        $pPDOStatement=NULL;

    }//if(...

    // Error
    ErrorDBLog( $sLogin, $sErrorTitle, $sMessage, $iReturn, TRUE);

    return $iReturn;
}
