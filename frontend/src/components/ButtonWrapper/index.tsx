import type { BoxProps } from '@mui/material'
import { Box } from '@mui/material'

function FormWrapper(props: BoxProps<'form'>) {
  return (
    <Box
      style={{
        width: '100%',
        display: 'flex',
        flexDirection: 'row',
        justifyContent: 'end',
        gap: '20px',
        margin: '20px 0',
      }}
    >
      {props.children}
    </Box>
  )
}

export default FormWrapper
